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
     * @param array{host: string, port: int, dbname: string, user: string, password: string, poolsize: int} $connectionParams
     */
    public function connect(array $connectionParams): ConnectionInterface
    {
        /*
         $poolsize = $connectionParams['poolsize'] ?? self::DEFAULT_POOL_SIZE;

        if (! isset(self::$pool)) {
             $config = (new PostgresConfig())
                 ->withHost($connectionParams['host'])
                 ->withPort($connectionParams['port'])
                 ->withDbname($connectionParams['dbname'])
                 ->withUsername($connectionParams['user'])
                 ->withPassword($connectionParams['password']);

             self::$pool = new ClientPool(PostgresClientFactory::class, $config, $connectionParams['poolsize']);
             self::$pool->fill();
         }

         $connection = self::$pool->get();
         defer(static fn() => self::$pool->put($connection));
        */

        $connection = $this->createNewConnection($connectionParams);
        // Free connection
        defer(static fn() => $connection->reset());

        return new Connection($connection);
    }

    private function createNewConnection(array $connectionParams): PostgreSQL
    {
        $config = (new PostgresConfig())
            ->withHost($connectionParams['host'])
            ->withPort($connectionParams['port'])
            ->withDbname($connectionParams['dbname'])
            ->withUsername($connectionParams['user'])
            ->withPassword($connectionParams['password'] . ';application_name=' . env('APP_NAME'));

        $newConnection = new PostgreSQL();
        $newConnection->connect($config->getConnectionString());

        return $newConnection;
    }
}
