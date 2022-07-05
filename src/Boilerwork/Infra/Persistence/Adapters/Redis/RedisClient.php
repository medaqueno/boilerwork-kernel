#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\Adapters\Redis;

use Redis;

/**
 * Open a pool of connections to Redis server, so we can use them when needed.
 *
 * @example []
        $redisClient = new RedisClient();
        $redisClient->getConnection();
        $redisClient->hSet('SetOneKey', 'hashKey', json_encode(['foo' => 'bar']));
        var_dump($redisClient->hGet('SetOneKey', 'hashKey'));
        $redisClient->putConnection();  // Connection must be released
 **/
final class RedisClient
{
    private Redis $conn;

    private readonly RedisPool $pool;

    public function __construct()
    {
        $this->pool = \Boilerwork\System\Container\Container::getInstance()->get(RedisPool::class);
    }

    public function getConnection(): void
    {
        $this->conn = $this->pool->getConn();
    }

    /**
     * Put connection back to the pool in order to be reused
     **/
    public function putConnection(): void
    {
        $this->pool->putConn($this->conn);
    }

    public function hGet($key, $hashKey): string|bool
    {
        return $this->conn->hGet($key, $hashKey);
    }

    public function hGetAll($key): array
    {
        return $this->conn->hGetAll($key);
    }

    public function hSet($key, $hashKey, $value): int|bool
    {
        return $this->conn->hSet($key, $hashKey, $value);
    }

    public function initTransaction(): Redis
    {
        return $this->conn->multi();
    }

    public function endTransaction()
    {
        return $this->conn->exec();
    }
}
