#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Adapters\Redis;

use Boilerwork\Persistence\Pools\RedisPool;
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
        $this->pool = globalContainer()->get(RedisPool::class);
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

    public function hExists($key, $hashKey): mixed
    {
        return $this->conn->hExists($key, $hashKey);
    }

    public function exists($key): int|bool
    {
        return $this->conn->exists($key);
    }

    public function get($key): mixed
    {
        return $this->conn->get($key);
    }

    public function set($key, $value, $timeout = null): mixed
    {
        return $this->conn->set($key, $value, $timeout);
    }

    public function del($key, ...$otherKeys): mixed
    {
        return $this->conn->del($key, ...$otherKeys);
    }

    public function expire($key, $ttl): bool
    {
        return $this->conn->expire($key, $ttl);
    }

    public function raw($method, ...$params): mixed
    {
        return $this->conn->$method(...$params);
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
