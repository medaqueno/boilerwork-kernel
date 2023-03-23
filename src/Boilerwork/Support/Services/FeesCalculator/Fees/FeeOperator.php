#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\Fees;

use Boilerwork\Support\Services\FeesCalculator\ValueObjects\Fee;

final class FeeOperator extends Fee implements FeeInterface
{
    const DESCRIPTION = 'operator';

    public function toDTO(): FeeDTO
    {
        return new FeeDTO(
            fee: $this::DESCRIPTION,
            amount: $this->value(),
            type: $this->type(),
            exchange: null
        );
    }

    public function percent(float $val): float
    {
        bcscale(10);
        $res = bcadd(
            (string) $val,
            bcmul(
                (string) $val,
                bcdiv((string) $this->value(), '100')
            )
        );
        return (float) $res;
    }

    public function total(float $val): float
    {
        bcscale(10);
        $res = bcadd(
            (string) $val,
            (string) $this->value()
        );
        return (float) $res;
    }
}
