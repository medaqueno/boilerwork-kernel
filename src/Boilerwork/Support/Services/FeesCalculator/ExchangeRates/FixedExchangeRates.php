#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ExchangeRates;

class FixedExchangeRates implements ExchangeRatesInterface
{

    public function __construct(private float $fixExchange)
    {

    }

    public function exchangeRate(string $currencyFrom, string $currencyTo): float
    {
        return $this->fixExchange;
    }
}


