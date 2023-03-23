#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\Fees;

interface FeeInterface
{
    public function percent(float $val): float;
    public function total(float $val): float;
    public function type(): string;
    public function toDTO(): FeeDTO;
}
