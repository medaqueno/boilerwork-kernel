#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ValueObjects\ServiceType;

/**
 * Generic authorization used by all Services
 */
enum ServiceTypeProvider: string
{
    case ACCOMODATION = 'accommodation';
    case FLIGHT = 'flight';
    case ACTIVITY = 'activity';
    case TRANSFER = 'transfer';
    case PACKAGE = 'package';
    case INSURANCE = 'insurance';

    public static function isValid(string $value): bool
    {
        return is_null(self::tryFrom($value));
    }
}
