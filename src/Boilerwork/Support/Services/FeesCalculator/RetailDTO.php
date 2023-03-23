#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator;

use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Fee;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price\Price;
use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Price\PriceCompany;

final class RetailDTO {
    public function __construct(
        public readonly PriceCompany $price,
        public readonly array $fees
    ){}
}

