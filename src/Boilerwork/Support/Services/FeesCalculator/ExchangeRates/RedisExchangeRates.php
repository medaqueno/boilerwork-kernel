#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ExchangeRates;

use Boilerwork\Persistence\Adapters\Redis\RedisClient;

class RedisExchangeRates implements ExchangeRatesInterface
{
    private array $cache = [];

    private function exchange(string $from, string $to): float
    {
        $key = sprintf('%s-%s', $from, $to);
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $redis = new RedisClient();
        $redis->getConnection();
        $rates = $redis->rawCommand('JSON.GET', 'exchange_rates', "$.$from", "$.$to");
        $rates = json_decode($rates, true);
        $redis->putConnection();

        $baseValue = array_key_exists('$.'. $from, $rates) ? (float) $rates['$.' . $from][0] : 1;
        $codeValue = array_key_exists('$.'. $to, $rates) ? (float) $rates['$.' . $to][0] : 1;

        bcscale(6);
        $this->cache[$key] = (float) bcmul((string) $codeValue, bcdiv('1', (string) $baseValue));
        return $this->cache[$key];
    }

    public function exchangeRate(string $currencyFrom, string $currencyTo): float
    {
        return $this->exchange($currencyFrom, $currencyTo);
    }
}
