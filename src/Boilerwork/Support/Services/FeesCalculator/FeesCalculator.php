#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator;

use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price\Price;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeForex;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price\PriceCompany;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeInterface;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\ServiceType\ServiceType;

final class FeesCalculator
{
    private array $fees;
    private Price $purchasePrice;
    private float $valuePrice;

    //patron strategy para cada fee
    public function __construct(private FeesCalculatorDataProvider $dataProvider)
    {
    }

    private function applyFee(FeeInterface $fee): self
    {
        // var_dump('applyFee');
        // TODO: mirar esto $fee->$fee->type()($this->valuePrice)
        $this->valuePrice = match ($fee->type()) {
            'total' => $fee->total($this->valuePrice),
            'percent' => $fee->percent($this->valuePrice),
        };
        // var_dump(sprintf('%s %s %s', $this->valuePrice, $fee->type(), $fee::DESCRIPTION));

        $this->fees[] = $fee->toDTO();
        return $this;
    }

    private function applyFeeIfNetPrice(FeeInterface $fee): self
    {
        if ($this->purchasePrice->type()->toPrimitive() == 'retail') {
            return $this;
        }
        return $this->applyFee($fee);
    }

    private function setPrice(Price $price): self
    {
        $this->fees = [];
        $this->purchasePrice = $price;
        $this->valuePrice = $price->amount();
        return $this;
    }

    /* TODO: cambiar priceCompany por priceCalculated*/
    private function salePrice(): PriceCompany
    {
        return PriceCompany::fromData('sale', $this->valuePrice, $this->dataProvider->tenantCurrency);
    }

    private function exchangeForexFee(): FeeForex
    {
        if ($this->dataProvider->tenantCurrency === $this->purchasePrice->iso3()) {
            // devolvemos el por defecto con exchange 1
            return $this->dataProvider->forexFee;
        }

        return new FeeForex(
            $this->dataProvider->forexFee->value(),
            $this->dataProvider->forexFee->type(),
            $this->dataProvider->exchangeRate($this->purchasePrice->iso3())
        );
    }

    private function applyForexFee(): self
    {
        return $this->applyFee($this->exchangeForexFee());
    }

    private function applyOperatorFee(?string $serviceType = null): self
    {
        return $this->applyFeeIfNetPrice($this->dataProvider->operatorFee($serviceType));
    }

    private function applyAgencyFee(?string $serviceType = null): self
    {
        return $this->applyFeeIfNetPrice($this->dataProvider->agencyFee($serviceType));
    }

    public function saleWithFees(Price $price, ?ServiceType $serviceType = null): RetailDTO
    {
        $serviceFeeType = isset($serviceType) ? $serviceType->serviceFeeType() : null;

        $salePrice = $this->setPrice($price)
            ->applyForexFee()
            ->applyOperatorFee($serviceFeeType)
            ->applyAgencyFee($serviceFeeType)
            ->salePrice();

        return new RetailDTO($salePrice, $this->fees);
    }

    public function sale(Price $price, ?ServiceType $serviceType = null): PriceCompany
    {
        $pvp = $this->saleWithFees($price, $serviceType);
        return $pvp->price;
    }

    static public function create(
        string $serviceType,
        string $idTenant,
        ?string $idCart = null,
        ?array $servicesInCart = []
    ): self {
        $redisProvider = new DataProviderRedis();
        return new static(
            // data source
            $redisProvider->dataProvider(
                $serviceType,
                $idTenant,
                $idCart,
                $servicesInCart,
            )
        );
    }
}
