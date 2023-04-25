#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Pools;

use Boilerwork\Support\Singleton;
use Redis;
use OpenSwoole\Core\Coroutine\Client\RedisClientFactory;
use OpenSwoole\Core\Coroutine\Client\RedisConfig;
use OpenSwoole\Core\Coroutine\Pool\ClientPool;

final class RedisPool
{
    use Singleton;

    public readonly ClientPool $pool;

    /**
     * PostgresqlPool constructor.
     */
    public function __construct()
    {
        $host     = env('REDIS_HOST');
        $port     = env('REDIS_PORT') ?? 6379;
        $password = env('REDIS_PASSWORD') ?? '';
        $size     = env('REDIS_SIZE_CONN') ?? 4;


        $config = (new RedisConfig())
            ->withDbIndex(0)
            ->withHost($host)
            ->withPort((int)$port);

        if ($password !== '') {
            $config->withAuth($password);
        }

        $this->pool = new ClientPool(RedisClientFactory::class, $config, (int)$size);
        $this->pool->fill();
    }

    public function getConn(float $timeOut = -1): Redis
    {
        return $this->pool->get($timeOut);
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
