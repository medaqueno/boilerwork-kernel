#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price;

use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Money\Money;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price\PriceCompanyType;
use Boilerwork\Foundation\ValueObjects\ValueObject;

final class PriceCompany extends ValueObject
{
    public function __construct(
        private PriceCompanyType $type,
        private Money $money
    )
    {
    }

    public static function fromData(string $type, float $amount, string $iso3): static
    {
        $type = new PriceCompanyType($type);
        $money = Money::fromData($amount, $iso3);

        return new static ($type, $money);
    }

    public function type(): PriceCompanyType
    {
        return $this->type;
    }

    public function money(): Money
    {
        return $this->money;
    }



    public function amount(): float
    {
        return $this->money()->amount();
    }

    public function iso3(): string
    {
        return $this->money()->currency()->isoCode();
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type->toPrimitive(),
            'amount' => $this->money()->rounded(),
            'currency' => $this->money()->currency()->toArray()
        ];
    }

}
