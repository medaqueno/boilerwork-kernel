#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ValueObjects\Money;

use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Currency\Currency;
use Boilerwork\Foundation\ValueObjects\ValueObject;

final class Money extends ValueObject
{
    public function __construct(
        private float $amount,
        private Currency $currency
    )
    {
    }

    public static function fromData(float $amount, string $iso3): static
    {
        $iso3 = Currency::fromIsoCode($iso3);

        return new static ($amount, $iso3);
    }

    public function toPrimitive(): string
    {
        return sprintf('%s %s', $this->currency->toPrimitive(), $this->rounded());
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function rounded(): float
    {
        return (float) number_format($this->amount, $this->currency->precision(), '.', '');
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->rounded(),
            'symbol' => $this->currency->toArray()
        ];
    }
}
