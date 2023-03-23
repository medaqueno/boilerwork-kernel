#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ValueObjects\Currency;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;

class CurrencySymbol extends ValueObject
{
    public function __construct(
        public readonly string $value
    )
    {
        Assert::lazy()->tryAll()
            ->that($value)
            ->string('Currency symbol id is not string', 'currencySymbol.invalidType')
            ->verifyNow();
    }

    public function toPrimitive(): string
    {
        return $this->value;
    }
}
