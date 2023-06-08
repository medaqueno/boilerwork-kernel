#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator;

use Boilerwork\Persistence\Adapters\Redis\RedisAdapter;
use Boilerwork\Persistence\Pools\RedisPool;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeForex;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeAgency;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeOperator;
use Boilerwork\Support\Services\FeesCalculator\FeeToApplyProvider;
use Boilerwork\Support\Services\FeesCalculator\FeesCalculatorDataProvider;
use Boilerwork\Support\Services\FeesCalculator\ExchangeRates\RedisExchangeRates;
use Boilerwork\Support\Services\FeesCalculator\FeesCalculatorTenantCacheException;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\ServiceType\ServiceType;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\ServiceType\ServiceFeeTypeProvider;

class DataProviderRedis implements IFeesDataProvider
{
    private readonly RedisAdapter $redis;
    public function __construct()
    {
        $this->redis = new RedisAdapter(new RedisPool());
    }

    private function keyServices(string $cartId): string
    {
        return sprintf('crm:travel:%s:services', $cartId);
    }

    private function keyTenantCurrency(string $tenantId): string
    {
        return sprintf('clients:tenant:%s:currency', $tenantId);
    }

    private function keyTenantFees(string $tenantId): string
    {
        return sprintf('clients:tenant:%s:fees', $tenantId);
    }

    public function dataProvider(
        string $serviceType,
        string $idTenant,
        ?string $idCart = null,
        ?array $servicesInCart = [],
    ): FeesCalculatorDataProvider {
        $serviceFeeType = (new ServiceType($serviceType))->serviceFeeType();
        $tenantCurrency = $this->redis->get($this->keyTenantCurrency($idTenant));
        if (!$tenantCurrency) {
            throw new FeesCalculatorTenantCacheException(
                $idTenant,
                'tenantCurrencyNotFound.feesCalculatorBuilder',
                'Tenant currency not found.',
                404
            );
        }

        $servicesInCart = $this->servicesInCart($serviceFeeType, $servicesInCart);
        $feesToGetFromCache = $servicesInCart;
        if ($idCart) {
            $cartServices = $this->redis->get($this->keyServices($idCart));
            $servicesInCart = $cartServices ? json_decode($cartServices) : [];
            $feesToGetFromCache = [$serviceFeeType];
        }

        $feesTopApply = $this->feesToApply($servicesInCart);
        //var_dump($feesTopApply);
        $operatorFees = [];
        $agencyFees = [];
        foreach ($feesToGetFromCache as $service) {
            $keyAgencyFee = sprintf('$.agency.%s.%s', $service, $feesTopApply);
            $agencyFees[$keyAgencyFee] = $service;
            $keyOperatorFee = sprintf('$.operator.%s.%s', $service, $feesTopApply);
            $operatorFees[$keyOperatorFee] = $service;
        }
        $keyForexFee = '$.forex';

        $fees = $this->redis->rawCommand(
            'JSON.GET',
            $this->keyTenantFees($idTenant),
            $keyForexFee,
            ...array_keys(empty($agencyFees) ? [] : $agencyFees),
            ...array_keys(empty($operatorFees) ? [] : $operatorFees),
        );

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
        return array_unique(array_merge($servicesInCart, [$serviceFeeType]));
    }

    private function feesToApply(array $servicesInCart): string
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
