#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System;

use Boilerwork\Helpers\Singleton;
use Swoole\Table;
use Swoole\Timer;

final class RateLimiter
{
    use Singleton;

    public const MAX_REQUESTS = 2;

    public const MAX_TIME_SECONDS = 10;

    private static self $instance;

    protected Table $table;

    private function __construct()
    {
        $table = new Table(1024 * 1024);
        $table->column('ip', Table::TYPE_STRING, 16);
        $table->column('count', Table::TYPE_INT, 8);
        $table->create();
        $this->table = $table;
        Timer::tick(self::MAX_TIME_SECONDS * 1000, [$this, 'clear']);
    }

    public function clear(): void
    {
        // echo "clear \n ";
        foreach ($this->table as $key => $item) {
            // var_dump($key);
            $this->table->del($key);
        }
    }

    public function access(string $ip): int
    {
        $key  = substr(md5($ip), 0, 16);
        $record = $this->table->get($key);
        if ($record) {
            $this->table->set($key, [
                'count' => $record['count'] + 1,
            ]);
            return $record['count'] + 1;
        } else {
            $this->table->set($key, [
                'ip' => $ip,
                'count' => 1,
            ]);
            return 1;
        }
    }

    public function getIpAddress(): ?string
    {
        // print_r($_SERVER);
        // print_r($request);
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return null;
    }
}
