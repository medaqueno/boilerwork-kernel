#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ValueObjects\ServiceType;

enum ServiceFeeTypeProvider: string
{
    case ACCOMMODATION = 'accommodation';
    case TRANSPORT = 'transport';
    case INSURANCE = 'insurance';
    case ACTIVITY = 'activity';
    case TRANSFER = 'transfer';
    case CRUISE = 'cruise';
    case TOUR = 'tour';
    case PACKAGE = 'package';

    public static function getsTransportAccommodation(): array
    {
        return [
            self::ACCOMMODATION->value,
            self::TRANSPORT->value,
            self::INSURANCE->value,
        ];
    }
}
