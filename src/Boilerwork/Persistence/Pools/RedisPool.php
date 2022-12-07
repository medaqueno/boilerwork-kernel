#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Pools;

use Boilerwork\Support\Singleton;
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
    public function __construct()
    {
        $host = env('REDIS_HOST') ?? 'quadrant-redis';
        $port = env('REDIS_PORT') ?? 6379;
        $password = env('REDIS_PASSWORD') ?? '';
        $size = env('REDIS_SIZE_CONN') ?? 64;

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
