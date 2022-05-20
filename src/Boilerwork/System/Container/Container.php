#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Container;

use Psr\Container\ContainerInterface;
use Illuminate\Container\Container as ContainerImplementation;
use App\Core\BC\Domain\UserRepository;
use App\Core\BC\Infra\Persistence\UserInMemoryRepository;
use App\Core\BC\Infra\Persistence\UserPostgreSQLRepository;
use App\Core\BC\Infra\Persistence\UserRedisRepository;
use Boilerwork\System\Clients\MQTTPool;
use Boilerwork\System\Clients\PostgreSQLReadsPool;
use Boilerwork\System\Clients\PostgreSQLWritesPool;
use Boilerwork\System\Clients\RedisPool;

/**
 * Dependency Injection Container
 * Still deciding which one to use finally or if create manually a Service Container
 *
 * https://container.thephpleague.com/
 * https://laravel.com/api/9.x/Illuminate/Container/Container.html
 */
final class Container implements ContainerInterface
{
    private ContainerInterface $container;

    public function __construct()
    {
        $this->container = new ContainerImplementation();

        $this->container->bind(UserRepository::class, UserPostgreSQLRepository::class);

        // Start PostgreSQL Connection Pools Read and Writes to be used by services
        $this->container->bind(PostgreSQLReadsPool::class, function () {
            return PostgreSQLReadsPool::getInstance();
        });

        $this->container->bind(PostgreSQLWritesPool::class, function () {
            return PostgreSQLWritesPool::getInstance();
        });

        // Start Redis Connection Pool to be used by services
        $this->container->bind(RedisPool::class, function () {
            return RedisPool::getInstance();
        });

        // Start MQTT Connection Pool to be used by services
        $this->container->bind(MQTTPool::class, function () {
            return MQTTPool::getInstance();
        });
    }

    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }
}
