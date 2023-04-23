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
    public function __construct(private RedisPool $pool)
    {
    }

    private function execute(callable $callback)
    {
        $conn = $this->pool->getConn();
        try {
            return $callback($conn);
        } finally {
            $this->pool->putConn($conn);
        }
    }

    /**
     * Get the value of a key.
     *
     * @param string $key The key to retrieve the value for.
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
     * @param string $key The key to set the value for.
     * @param string $value The value to set for the key.
     * @param int|null $timeout The optional timeout in seconds. If not provided, the key will not expire.
     * @return bool True if the operation was successful, false otherwise.
     *
     * @example
     * $redisClient->set('example_key', 'example_value', 3600);
     */
    public function set(string $key, string $value, int $timeout = null): bool
    {
        return $this->execute(function (Redis $conn) use ($key, $value, $timeout) {
            return $conn->set($key, $value, $timeout);
        });
    }

    /**
     * Delete a key.
     *
     * @param string $key The key to delete.
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
     * Execute a series of commands in a transaction.
     *
     * @param callable $callback A function that takes a Redis connection as a parameter and performs the desired operations.
     * @return mixed[] The results of the executed commands in the transaction.
     *
     * @example
     * $results = $redisClient->multi(function (Redis $conn) {
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

    public function initTransaction(): Redis
    {
        return $this->execute(function (Redis $conn) {
            return $conn->multi();
        });
    }

    public function endTransaction()
    {
        return $this->execute(function (Redis $conn) {
            return $conn->exec();
        });
    }

    /**
     * Execute a raw Redis command.
     *
     * @param string $command The Redis command to execute.
     * @param mixed ...$arguments The arguments to pass to the command.
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

    /**
     * Set a key's time to live in seconds.
     *
     * @param string $key Redis key
     * @param int $ttl Time to live in seconds
     * @return bool
     *
     * @example
     * $redisClient->expire('my_key', 60);
     */
    public function expire(string $key, int $ttl): bool
    {
        return $this->execute(function (Redis $conn) use ($key, $ttl) {
            return $conn->expire($key, $ttl);
        });
    }

    /**
     * Add one or more members to a set.
     *
     * @param string $key Redis key
     * @param mixed ...$members Members to add
     * @return int|bool
     *
     * @example
     * $redisClient->sAdd('my_set', 'member1', 'member2', 'member3');
     */
    public function sAdd(string $key, ...$members): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $members) {
            return $conn->sAdd($key, ...$members);
        });
    }

    /**
     * Get all members of a set
     *
     * @param string $key
     * @return array
     */
    public function sMembers(string $key): array
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->sMembers($key);
        });
    }

    /**
     * Remove one or more members from a set
     *
     * @param string $key
     * @param mixed ...$members
     * @return int|bool
     */
    public function sRem(string $key, ...$members): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $members) {
            return $conn->sRem($key, ...$members);
        });
    }

    /**
     * Add one or more elements to the end of a list.
     *
     * @param string $key Redis key
     * @param mixed ...$values Values to add
     * @return int|bool
     *
     * @example
     * $redisClient->rPush('my_list', 'value1', 'value2', 'value3');
     */
    public function rPush(string $key, ...$values): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $values) {
            return $conn->rPush($key, ...$values);
        });
    }

    /**
     * Get a range of elements from a list.
     *
     * @param string $key Redis key
     * @param int $start Start index
     * @param int $end End index
     * @return array
     *
     * @example
     * $elements = $redisClient->lRange('my_list', 0, 2);
     */
    public function lRange(string $key, int $start, int $end): array
    {
        return $this->execute(function (Redis $conn) use ($key, $start, $end) {
            return $conn->lRange($key, $start, $end);
        });
    }

    /**
     * Remove and return the first element of a list
     *
     * @param string $key
     * @return mixed
     */
    public function lPop(string $key): mixed
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->lPop($key);
        });
    }


    /**
     * Set the value of an element in a list by its index
     *
     * @param string $key
     * @param int $index
     * @param mixed $value
     * @return bool
     */
    public function lSet(string $key, int $index, $value): bool
    {
        return $this->execute(function (Redis $conn) use ($key, $index, $value) {
            return $conn->lSet($key, $index, $value);
        });
    }

    /**
     * Remove elements from a list by value.
     *
     * @param string $key Redis key
     * @param mixed $value Value to remove
     * @param int $count Number of occurrences to remove
     * @return int|bool
     *
     * @example
     * $redisClient->lRem('my_list', 'value1', 1);
     */
    public function lRem(string $key, int $count, $value): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $count, $value) {
            return $conn->lRem($key, $count, $value);
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

    /**
     * Get the value of a hash field.
     *
     * @param string $key Redis key
     * @param string $field Hash field
     * @return string|null
     *
     * @example
     * $value = $redisClient->hGet('my_hash', 'field1');
     */
    public function hGet(string $key, string $hashKey): string|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $hashKey) {
            return $conn->hGet($key, $hashKey);
        });
    }

    /**
     * Get all fields and values of a hash.
     *
     * @param string $key Redis key
     * @return array
     *
     * @example
     * $hash = $redisClient->hGetAll('my_hash');
     */
    public function hGetAll(string $key): array
    {
        return $this->execute(function (Redis $conn) use ($key) {
            return $conn->hGetAll($key);
        });
    }

    // hSet
    /**
     * Set the string value of a hash field.
     *
     * @param string $key Redis key
     * @param string $field Hash field
     * @param string $value Value to set
     * @return bool
     *
     * @example
     * $redisClient->hSet('my_hash', 'field1', 'value1');
     */
    public function hSet(string $key, string $hashKey, $value): int|bool
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


    /**
     * Delete one or more hash fields.
     *
     * @param string $key Redis key
     * @param mixed ...$fields Fields to delete
     * @return int|bool
     *
     * @example
     * $redisClient->hDel('my_hash', 'field1', 'field2');
     */
    public function hDel(string $key, ...$fields): int|bool
    {
        return $this->execute(function (Redis $conn) use ($key, $fields) {
            return $conn->hDel($key, ...$fields);
        });
    }


    // JSON Helpers

    /**
     * Set the JSON value at the specified key.
     *
     * @param string $key Redis key
     * @param string $payload JSON string
     * @return mixed
     *
     * @example
     * $redisClient->jsonSet('my_key', '{"name": "John", "age": 30}');
     */

    public function jsonSet(string $key, string $payload): mixed
    {
        return $this->execute(function (Redis $conn) use ($key, $payload) {
            return $conn->rawCommand('JSON.SET', $key, '$', $payload);
        });
    }

    // jsonSetRaw
    /**
     * Set the JSON value at the specified key and path.
     *
     * @param string $key Redis key
     * @param string $payload JSON string
     * @param string $path JSON path
     * @return mixed
     *
     * @example
     * $redisClient->jsonSetRaw('my_key', '{"name": "John", "age": 30}', '$.user');
     */
    public function jsonSetRaw(string $key, string $payload, string $path): mixed
    {
        return $this->execute(function (Redis $conn) use ($key, $payload, $path) {
            return $conn->rawCommand(
                'JSON.SET',
                $key,
                $path,
                $payload
            );
        });
    }

    /**
     * Get the JSON value at the specified key and conditions.
     *
     * @param string $key Redis key
     * @param array ...$conditions Conditions for filtering the JSON value
     * @return array|null
     *
     * @example
     * $redisClient->jsonGet('my_key', ['age', '>', '25'], ['city', '==', 'New York']);
     */
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
                    $conds
                );

                return is_string($res) ? json_decode($res, true) : null;
            } else {
                $res = $conn->rawCommand(
                    'JSON.GET',
                    $key
                );

                return is_string($res) ? json_decode($res, true) : null;
            }
        });
    }


    /**
     * @param string $key
     * @param array<attribute,operator,value> ...$conditions Conditions for filtering the JSON value
     *
     * @desc Build from variadic a string like: '$..[?(@.attr1=="foo" || @.attr2=="bar")]'
     *      Possible Operators: ==, >, <, >=, <=, !=
     *
     * $redisClient->jsonGetOr('my_key', ['age', '>', '25'], ['city', '==', 'New York']);
     *
     * @return array
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
                    $conds
                );

                return is_string($res) ? json_decode($res, true) : null;
            } else {
                $res = $conn->rawCommand(
                    'JSON.GET',
                    $key
                );

                return is_string($res) ? json_decode($res, true) : null;
            }
        });
    }

    /**
     * Get the JSON value at the specified key and path.
     *
     * @param string $key Redis key
     * @param string $path JSON path
     * @return array|null
     *
     * @example
     * $redisClient->jsonGetRaw('my_key', '$.user.name');
     */
    public function jsonGetRaw(string $key, string $path): array|null
    {
        return $this->execute(function (Redis $conn) use ($key, $path) {
            $res = $conn->rawCommand(
                'JSON.GET',
                $key,
                $path
            );

            return is_string($res) ? json_decode($res, true) : null;
        });
    }
}
