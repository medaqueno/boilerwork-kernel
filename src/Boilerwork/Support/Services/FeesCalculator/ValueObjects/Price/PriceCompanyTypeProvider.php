#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price;

enum PriceCompanyTypeProvider: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case CANCELLATION = 'cancellation';
}
