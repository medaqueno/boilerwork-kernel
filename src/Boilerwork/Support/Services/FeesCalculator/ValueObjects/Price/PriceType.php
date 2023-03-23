#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price;

use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price\PriceTypeProvider;
use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;

final class PriceType extends ValueObject
{
    public function __construct(
        private string $value
    )
    {
        Assert::lazy()->tryAll()
            ->that($value)
            ->notEmpty('PriceType must not be empty', 'priceType.notEmpty')
            ->that(PriceTypeProvider::tryFrom($value))
            ->notNull('PriceType not found in provider', 'priceType.notFound')
            ->verifyNow();
    }

    public function toPrimitive(): string
    {
        return $this->value;
    }
}
