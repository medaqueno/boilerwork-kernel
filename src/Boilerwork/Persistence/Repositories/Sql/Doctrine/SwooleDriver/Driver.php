#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Doctrine\SwooleDriver;

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use OpenSwoole\Core\Coroutine\Client\PostgresClientFactory;
use OpenSwoole\Core\Coroutine\Client\PostgresConfig;
use OpenSwoole\Core\Coroutine\Pool\ClientPool;
use OpenSwoole\Coroutine\PostgreSQL;

final class Driver extends AbstractPostgreSQLDriver
{
    public const DEFAULT_POOL_SIZE = 2;

    private static ClientPool $pool;

    /**
     * @param array{host: string, port: int, dbname: string, user: string, password: string, poolsize: int} $params
     */
    public function connect(array $params): ConnectionInterface
    {
        if (! isset(self::$pool)) {
            $config = (new PostgresConfig())
                ->withHost($params['host'])
                ->withPort($params['port'])
                ->withDbname($params['dbname'])
                ->withUsername($params['user'])
                ->withPassword($params['password']);

            self::$pool = new ClientPool(PostgresClientFactory::class, $config, $params['poolsize']);
            self::$pool->fill();
        }

        $connection = self::$pool->get();

        defer(static fn() => self::$pool->put($connection));
        return new Connection($connection);
    }
}
