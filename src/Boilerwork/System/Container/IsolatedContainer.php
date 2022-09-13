#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Container;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Container\EntryNotFoundException;
use Psr\Container\ContainerInterface;

/**
 * Local Dependency Container
 * Isolated from other processes or coroutines
 */
final class IsolatedContainer implements ContainerInterface
{
    private static $instances = [];

    /**
     * Retrieve instance from local container
     * or if it does not exist, try to retrieve it from Global Container
     *
     * @throws Exception
     * @throws EntryNotFoundException
     */
    public function get($id): mixed
    {
        return self::$instances[$id] ?? globalContainer()->get($id);
    }

    /**
     * Check if instance exist in local container or global container
     *
     * @param string $id
     * @return bool
     */
    public function has($id): bool
    {
        return array_key_exists($id, self::$instances) || globalContainer()->has($id);
    }

    public function instance($abstract, $instance): void
    {
        static::$instances[$abstract] = $instance;
    }
}
