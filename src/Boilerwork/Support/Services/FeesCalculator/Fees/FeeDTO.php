#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\Fees;

final class FeeDTO {
    public float $exchange;
    public function __construct(
        public readonly string $fee,
        public readonly float $amount,
        public readonly string $type,
        ?float $exchange
    ){
        if(isset($exchange)){
            $this->exchange = $exchange;
        }
    }
}

