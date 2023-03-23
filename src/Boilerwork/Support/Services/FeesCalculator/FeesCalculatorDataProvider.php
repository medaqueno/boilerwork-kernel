#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator;

use Boilerwork\Support\Services\FeesCalculator\Fees\FeeForex;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeAgency;
use Boilerwork\Support\Services\FeesCalculator\Fees\FeeOperator;
use Boilerwork\Support\Services\FeesCalculator\ExchangeRates\ExchangeRatesInterface;


class FeesCalculatorDataProvider
{
    public readonly FeeOperator $operatorFee;
    public readonly FeeAgency $agencyFee;

    public function __construct(
        public readonly string $serviceFeeType,
        public readonly string $tenantCurrency,
        public readonly array $operatorFees, // array de los tipos que se pueden aplicar
        public readonly array $agencyFees,
        public readonly FeeForex $forexFee,
        private ExchangeRatesInterface $ratesExchange
    )
    {
        $this->operatorFee = $operatorFees[$serviceFeeType];
        $this->agencyFee = $agencyFees[$serviceFeeType];
    }

    public function exchangeRate(string $currencyFrom): float
    {
        return $this->ratesExchange->exchangeRate($currencyFrom, $this->tenantCurrency);
    }

    public function operatorFee(?string $serviceFeeType = null): FeeOperator
    {
        return $this->operatorFees[$serviceFeeType?? $this->serviceFeeType];
    }

    public function agencyFee(?string $serviceFeeType = null): FeeAgency
    {
        return $this->agencyFees[$serviceFeeType?? $this->serviceFeeType];
    }
}
