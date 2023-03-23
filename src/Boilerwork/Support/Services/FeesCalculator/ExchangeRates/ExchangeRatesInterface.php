#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ExchangeRates;

interface ExchangeRatesInterface
{
    public function exchangeRate(string $currencyFrom, string $currencyTo): float;
}
