#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price;

enum PriceTypeProvider: string
{
    case NET = 'net';
    case RETAIL = 'retail';
}
