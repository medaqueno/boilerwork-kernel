#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Adapters\Redis;

use Boilerwork\Persistence\Pools\RedisPool;
use Redis;

/**
 * Class RedisAdapter
 *
 * A Redis client that manages connections using a connection pool.
 * Automatically handles connections for each method call.
 *
 */
final class RedisAdapter
{
    private ?Redis $currentConn = null;
    private int $connectionUsageCount = 0;
    private bool $persistentConnection = false;
    private bool $inTransaction = false;

    public function __construct(private RedisPool $pool)
    {
    }

    private function execute(callable $callback)
    {
        $conn = $this->getConnection();
        try {
            return $callback($conn);
        } finally {
            $this->releaseConnection();
        }
    }

    private function getConnection(): Redis
    {
        if ($this->currentConn === null) {
            $this->currentConn = $this->pool->getConn();
        }
        $this->connectionUsageCount++;

        return $this->currentConn;
    }

    private function releaseConnection(): void
    {
        if (! $this->persistentConnection) {
            $this->connectionUsageCount--;
            if ($this->connectionUsageCount <= 0) {
                $this->pool->putConn($this->currentConn);
                $this->currentConn = null;
            }
        }
    }

    public function currentConnection(): ?Redis
    {
        return $this->currentConn;
    }

    /**
     * Use the same connection from pool.
     *
     *
     * @example
     * public function foo1()
     * {
     *  $this->redisClient->keepConnection(); // Keep connection
     *  $this->redisClient->set('example_key', 'example_value', 3600);
     *  $this->bar();
     *  $this->redisClient->releasePersistentConnection(); // Release connection
     * }
     * public function bar()
     * {
     *  $value = $this->redisClient->get('example_key');
     * }
     */
    public function keepConnection(): void
    {
        $this->persistentConnection = true;
    }

    public function releasePersistentConnection(): void
    {
        if ($this->persistentConnection) {
            $this->persistentConnection = false;
            $this->releaseConnection();
        }
    }

    public function scan(&$iterator, string $pattern, int $count = 0): mixed
    {
        return $this->execute(function (Redis $conn) use (&$iterator, $pattern, $count) {
            return $conn->scan($iterator, $pattern, $count);
        });
    }

    /**
     * Get the value of a key.
     *
     * @param  string  $key  The key to retrieve the value for.
     *
     * @return mixed The value associated with the key, or false if the key does not exist.
     *
     * @example
     * $value = $redisClient->get('example_key');
     */
    public function get(string $key): mixed
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->get($key);
        });
    }

    /**
     * Set the value of a key with an optional timeout.
     *
     * @param  string  $key  The key to set the value for.
     * @param  mixed|string  $value  The value to set for the key.
     * @param  int|null  $timeout  The optional timeout in seconds. If not provided, the key will not expire.
     *
     * @return bool True if the operation was successful, false otherwise.
     *
     * @example
     * $redisClient->set('example_key', 'example_value', 3600);
     */
    public function set(string $key, mixed $value, int|array $timeout = null): bool
    {
        return $this->execute(function (Redis $conn) use ($key, $value, $timeout) {
            return $conn->set($key, $value, $timeout);
        });
    }

    /**
     * Delete a key.
     *
     * @param  string  $key  The key to delete.
     *
     * @return int The number of keys that were removed.
     *
     * @example
     * $redisClient->del('example_key');
     */
    public function del(string $key): int
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->del($key);
        });
    }

    /**
     * Check if a key exists
     *
     * @param  string  $key  The key to delete.
     *
     * @return bool true if exists
     *
     * @example
     * $redisClient->exists('example_key');
     */
    public function exists(string $key): bool
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->exists($key);
        });
    }

    /**
     * Execute a series of commands in a transaction.
     *
     * @param  callable  $callback  A function that takes a Redis connection as a parameter and performs the desired operations.
     *
     * @return mixed[] The results of the executed commands in the transaction.
     *
     * @example
     * $results = $redisClient->multi(function () {
     *     $conn->set('key1', 'value1');
     *     $conn->set('key2', 'value2');
     *     $conn->get('key1');
     * });
     * // $results will contain an array of the results of each command in the transaction.
     */
    public function multi(callable $callback)
    {
        return $this->execute(function (Redis $conn) use ($callback) {
            $conn->multi();
            try {
                $callback($conn);

                return $conn->exec();
            } catch (\Exception $e) {
                $conn->discard();
                throw $e;
            }
        });
    }

    /**
     * Init a transaction that can be used in more than one method
     *
     * @example
     * public function foo1()
     * {
     *   $this->redisClient->keepConnection(); // Mantener la conexión
     *   $this->redisClient->beginTransaction(); // Iniciar la transacción
     *   $this->redisClient->set('example_key', 'example_value', 3600);
     *   $this->bar();
     *   $this->redisClient->commitTransaction(); // Confirmar la transacción
     *   $this->redisClient->releasePersistentConnection(); // Liberar la conexión
     * }
     */
    public function initTransaction(): void
    {
        if (! $this->inTransaction) {
            $this->execute(function (Redis $conn) {
                $conn->multi();
                $this->inTransaction = true;
            });
        }
    }

    public function endTransaction(): void
    {
        if ($this->inTransaction) {
            $this->execute(function (Redis $conn) {
                $conn->exec();
                $this->inTransaction = false;
            });
        }
    }

    public function discardTransaction(): void
    {
        if ($this->inTransaction) {
            $this->execute(function (Redis $conn) {
                $conn->discard();
                $this->inTransaction = false;
            });
        }
    }

    /**
     * Execute a raw Redis command.
     *
     * @param  string  $command  The Redis command to execute.
     * @param  mixed  ...$arguments  The arguments to pass to the command.
     *
     * @return mixed The result of the executed command.
     *
     * @example
     * $result = $redisClient->rawCommand('SET', 'example_key', 'example_value');
     */
    public function rawCommand(string $command, ...$arguments): mixed
    {
        return $this->execute(function (Redis $conn) use ($command, $arguments) {
            return $conn->rawCommand($command, ...$arguments);
        });
    }

    public function expire(string $key, int $ttl): bool
    {
        return $this->execute(function (Redis $conn) use ($key, $ttl) {
            return $conn->expire($key, $ttl);
        });
    }

    public function ttl(string $key): bool|int|Redis
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->ttl($key);
        });
    }

    public function sAdd(string $key, ...$members): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $members) {
            return $conn->sAdd($key, ...$members);
        });
    }

    public function rPush(string $key, ...$values): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $values) {
            return $conn->rPush($key, ...$values);
        });
    }

    public function lRange(string $key, int $start, int $end): array
    {
        return $this->execute(function (Redis $conn) use ($key, $start, $end) {
            return $conn->lRange($key, $start, $end);
        });
    }

    public function lPop(string $key): mixed
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->lPop($key);
        });
    }

    public function lSet(string $key, int $index, $value): bool
    {
        return $this->execute(function (Redis $conn) use ($key, $index, $value) {
            return $conn->lSet($key, $index, $value);
        });
    }

    public function lRem(string $key, $value, int $count): int|false
    {
        return $this->execute(function (Redis $conn) use ($key, $count, $value) {
            return $conn->lRem($key, $value, $count);
        });
    }

    public function incrBy(string $key, int $value): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $value) {
            return $conn->incrBy($key, $value);
        });
    }

    public function decrBy(string $key, int $value): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $value) {
            return $conn->decrBy($key, $value);
        });
    }

    public function lLen(string $key): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->lLen($key);
        });
    }

    public function sCard(string $key): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->sCard($key);
        });
    }

    public function sIsMember(string $key, $member): bool
    {
        return $this->execute(function (Redis $conn) use ($key, $member) {
            return $conn->sIsMember($key, $member);
        });
    }

    public function sMove(string $srcKey, string $dstKey, $member): bool
    {
        return $this->execute(function (Redis $conn) use ($srcKey, $dstKey, $member) {
            return $conn->sMove($srcKey, $dstKey, $member);
        });
    }

    public function sInter(string $key, string ...$keys): array
    {
        return $this->execute(function (Redis $conn) use ($key, $keys) {
            return $conn->sInter($key, ...$keys);
        });
    }

    public function sUnion(string $key, string ...$keys): array
    {
        return $this->execute(function (Redis $conn) use ($key, $keys) {
            return $conn->sUnion($key, ...$keys);
        });
    }

    public function sDiff(string $key, string ...$keys): array
    {
        return $this->execute(function (Redis $conn) use ($key, $keys) {
            return $conn->sDiff($key, ...$keys);
        });
    }

    public function hGet(string $key, string $hashKey): string|false
    {
        return $this->execute(function (Redis $conn) use ($key, $hashKey) {
            return $conn->hGet($key, $hashKey);
        });
    }

    public function hGetAll(string $key): array
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->hGetAll($key);
        });
    }

    public function hSet(string $key, string $hashKey, $value): int|false
    {
        return $this->execute(function (Redis $conn) use ($key, $hashKey, $value) {
            return $conn->hSet($key, $hashKey, $value);
        });
    }

    public function hExists(string $key, string $hashKey): mixed
    {
        return $this->execute(function (Redis $conn) use ($key, $hashKey) {
            return $conn->hExists($key, $hashKey);
        });
    }


    // JSON Helpers

    public function jsonSet(string $key, string $payload): mixed
    {
        return $this->execute(function (Redis $conn) use ($key, $payload) {
            return $conn->rawCommand('JSON.SET', $key, '$', $payload);
        });
    }

    public function jsonSetRaw(string $key, string $payload, string $path): mixed
    {
        return $this->execute(function (Redis $conn) use ($key, $payload, $path) {
            return $conn->rawCommand(
                'JSON.SET',
                $key,
                $path,
                $payload,
            );
        });
    }

    public function jsonGet(string $key, array ...$conditions): array|null
    {
        return $this->execute(function (Redis $conn) use ($key, $conditions) {
            if (count($conditions) > 0) {
                $conds = '$..[?(';

                foreach ($conditions as $item) {
                    $conds .= sprintf('@.%s%s"%s" && ', $item[0], $item[1], $item[2]);
                }

                $conds = rtrim($conds, " && ") . ')]';

                $res = $conn->rawCommand(
                    'JSON.GET',
                    $key,
                    $conds,
                );

                return is_string($res) ? json_decode($res, true) : null;
            } else {
                $res = $conn->rawCommand(
                    'JSON.GET',
                    $key,
                );

                return is_string($res) ? json_decode($res, true) : null;
            }
        });
    }


    /**
     * @param  string  $key
     * @param  array<attribute,operator,value>  $conditions
     *
     * @desc Build from variadic a string like: '$..[?(@.attr1=="foo" || @.attr2=="bar")]'
     *      Possible Operators: ==, >, <, >=, <=, !=
     *
     * @return array
     * @example -> jsonGet('keyName', ['attr1', '==', 'foo'], ['attr2', '==', 'bar']);
     *
     */
    public function jsonGetOr(string $key, array ...$conditions): array|null
    {
        return $this->execute(function (Redis $conn) use ($key, $conditions) {
            if (count($conditions) > 0) {
                $conds = '$..[?(';

                foreach ($conditions as $item) {
                    $conds .= sprintf('@.%s%s"%s" || ', $item[0], $item[1], $item[2]);
                }

                $conds = rtrim($conds, " || ") . ')]';

                $res = $conn->rawCommand(
                    'JSON.GET',
                    $key,
                    $conds,
                );

                return is_string($res) ? json_decode($res, true) : null;
            } else {
                $res = $conn->rawCommand(
                    'JSON.GET',
                    $key,
                );

                return is_string($res) ? json_decode($res, true) : null;
            }
        });
    }

    public function jsonGetRaw(string $key, string $path): array|null
    {
        return $this->execute(function (Redis $conn) use ($key, $path) {
            $res = $conn->rawCommand(
                'JSON.GET',
                $key,
                $path,
            );

            return is_string($res) ? json_decode($res, true) : null;
        });
    }
}
