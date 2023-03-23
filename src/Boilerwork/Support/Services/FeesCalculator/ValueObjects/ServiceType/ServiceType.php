#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ValueObjects\ServiceType;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;

final class ServiceType extends ValueObject
{
    public function __construct(
        private string $value
    )
    {
        Assert::lazy()->tryAll()
            ->that($value)
            ->notEmpty('ServiceType must not be empty', 'serviceType.notEmpty')
            ->that(ServiceTypeProvider::tryFrom($value))
            ->notNull('ServiceType not found in provider', 'serviceType.notFound')
            ->verifyNow();
    }

    public function serviceFeeType(): string
    {
        return match(ServiceTypeProvider::tryFrom($this->value))
            {
                ServiceTypeProvider::FLIGHT => ServiceFeeTypeProvider::TRANSPORT->value,
                default => $this->value
            };
    }

    public function toPrimitive(): string
    {
        return $this->value;
    }
}
