#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Clients;

use Boilerwork\Helpers\Singleton;
use Redis;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool as SwooleRedisPool;

final class RedisPool
{
    use Singleton;

    protected readonly SwooleRedisPool $pool;

    /**
     * PostgresqlPool constructor.
     */
    private function __construct()
    {
        $host = $_ENV['REDIS_HOST'] ?? 'redis-master';
        $port = $_ENV['REDIS_PORT'] ?? 6379;
        $password = $_ENV['REDIS_PASSWORD'] ?? '';
        $size = $_ENV['REDIS_SIZE_CONN'] ?? 64;

        $this->pool = new SwooleRedisPool(
            (new RedisConfig())
                ->withHost($host)
                ->withPort((int)$port),
            // ->withAuth('')
            // ->withDbIndex(0)
            // ->withTimeout((int)1),

            (int)$size
        );
    }

    public function getConn(): Redis
    {
        return $this->pool->get();
    }

    public function putConn(Redis $redis): void
    {
        $this->pool->put($redis);
    }

    public function close(): void
    {
        $this->pool->close();
        $this->pool = null;
    }
}
