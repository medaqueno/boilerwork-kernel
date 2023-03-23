#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator;

use Boilerwork\Support\Services\FeesCalculator\Fees\FeeForex;
use Boilerwork\Support\Services\FeesCalculator\FeeToApplyProvider;
use Boilerwork\Persistence\Adapters\Redis\RedisClient;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\ServiceType\ServiceType;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeAgency;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeOperator;
use Boilerwork\Support\Services\FeesCalculator\ExchangeRates\RedisExchangeRates;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\ServiceType\ServiceFeeTypeProvider;
use Boilerwork\Support\Services\FeesCalculator\FeesCalculatorTenantCacheException;
use Boilerwork\Support\Services\FeesCalculator\FeesCalculatorDataProvider;

class DataProviderRedis implements FeesCalculatorData
{
    static public function dataProvider(
        string $serviceType,
        string $idTenant,
        ?string $idCart = null,
        ?array $servicesInCart = [],
    ): FeesCalculatorDataProvider
    {
        $sss = new Static();
        $redisClient = new RedisClient();
        $redisClient->getConnection();

        $serviceFeeType = (new ServiceType($serviceType))->serviceFeeType();
        $tenantCurrency = $redisClient->get(sprintf('tenantdata_%s_currency', $idTenant));
        if (!$tenantCurrency) {
            throw new FeesCalculatorTenantCacheException(
                $idTenant,
                'tenantCurrencyNotFound.feesCalculatorBuilder',
                'Tenant currency not found.',
                404
            );
        }

        $servicesInCart = $sss->servicesInCart($serviceFeeType, $servicesInCart);
        $feesToGetFromCache = $servicesInCart;
        if ($idCart){
            $cartServices = $redisClient->get(sprintf('cart:%s:services', $idCart));
            $servicesInCart = $cartServices? json_decode($cartServices): [];
            $feesToGetFromCache = [$serviceFeeType];
        }

        $feesTopApply = $sss->feesToApply($servicesInCart);
        //var_dump($feesTopApply);
        $operatorFees = [];
        $agencyFees = [];
        foreach ($feesToGetFromCache as $service){
            $keyAgencyFee = sprintf('$.agency.%s.%s', $service, $feesTopApply);
            $agencyFees[$keyAgencyFee] = $service;
            $keyOperatorFee = sprintf('$.operator.%s.%s', $service, $feesTopApply);
            $operatorFees[$keyOperatorFee] = $service;
        }
        $keyForexFee = '$.forex';

        $fees = $redisClient->rawCommand(
            'JSON.GET',
            sprintf('tenantdata_%s_fees', $idTenant),
            $keyForexFee,
            ...array_keys(empty($agencyFees)? []: $agencyFees),
            ...array_keys(empty($operatorFees)? []: $operatorFees),
        );
        $redisClient->putConnection();

        if (!$fees) {
            throw new FeesCalculatorTenantCacheException(
                $idTenant,
                'tenantFeesNotFound.feesCalculatorBuilder',
                'Tenant fees not found.',
                404
            );
        }

        $fees = json_decode($fees);
        $operators = [];
        foreach ($operatorFees as $k => $v) {
            $fee = $fees->{$k}[0];
            $operators[$v] = new FeeOperator($fee->value, $fee->type);
        }

        $agencies = [];
        foreach ($agencyFees as $k => $v) {
            $fee = $fees->{$k}[0];
            $agencies[$v] = new FeeAgency($fee->value, $fee->type);
        }

        $ForexFee = $fees->{$keyForexFee}[0];

        return new FeesCalculatorDataProvider(
            serviceFeeType: $serviceFeeType,
            tenantCurrency: $tenantCurrency,
            operatorFees: $operators,
            agencyFees: $agencies,
            forexFee: new FeeForex($ForexFee->value, $ForexFee->type, 1),
            ratesExchange: new RedisExchangeRates() //TODO: enganchar interface
        );
    }

    private function servicesInCart(string $serviceFeeType, array $servicesInCart): array
    {
        return array_unique(array_merge( $servicesInCart, [$serviceFeeType]));
    }

    private function feesToApply( array $servicesInCart): string
    {
        if (count($servicesInCart) == 1) {
            return FeeToApplyProvider::SINGLE->value;
        }
        if (count(array_diff($servicesInCart, ServiceFeeTypeProvider::getsTransportAccommodation())) == 0) {
            return FeeToApplyProvider::TRANSPORT_ACCOMODATION->value;
        }
        return FeeToApplyProvider::COMBINED->value;
    }

}
