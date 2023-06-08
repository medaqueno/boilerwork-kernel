#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\ExchangeRates;

use Boilerwork\Persistence\Adapters\Redis\RedisAdapter;
use Boilerwork\Persistence\Pools\RedisPool;

class RedisExchangeRates implements ExchangeRatesInterface
{
    private RedisAdapter $redis;
    private array $cache = [];

    public function __construct()
    {
        $this->redis = new RedisAdapter(new RedisPool());
    }

    private function exchange(string $from, string $to): float
    {
        $key = sprintf('%s-%s', $from, $to);
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $rates = $this->redis->rawCommand('JSON.GET', 'masters:currency:exchange-rates', "$.$from", "$.$to");
        $rates = json_decode($rates, true);

        $baseValue = array_key_exists('$.' . $from, $rates) ? (float) $rates['$.' . $from][0] : 1;
        $codeValue = array_key_exists('$.' . $to, $rates) ? (float) $rates['$.' . $to][0] : 1;

        bcscale(6);
        $this->cache[$key] = (float) bcmul((string) $codeValue, bcdiv('1', (string) $baseValue));
        return $this->cache[$key];
    }

    public function exchangeRate(string $currencyFrom, string $currencyTo): float
    {
        return $this->exchange($currencyFrom, $currencyTo);
    }
}
