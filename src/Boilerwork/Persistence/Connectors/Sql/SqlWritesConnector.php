#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Connectors\Sql;

use Boilerwork\Persistence\Pools\PostgreSQLWritesPool;
use Boilerwork\Support\Singleton;
use Swoole\Coroutine\PostgreSQL;

final class SqlWritesConnector
{
    use Singleton;

    public static ?PostgreSQLWritesPool $pool = null;

    private function __construct()
    {
        $this->initConn();
    }

    public function initConn()
    {
        if (self::$pool === null) {

            self::$pool = new PostgreSQLWritesPool();
            $connectionSize = (int)swoole_cpu_num(); // Will open a channel per swoole worker

            self::$pool->initPool(
                host: env('POSTGRESQL_WRITES_HOST'),
                port: (int)env('POSTGRESQL_WRITES_PORT'),
                dbname: env('POSTGRESQL_WRITES_DBNAME'),
                username: env('POSTGRESQL_WRITES_USERNAME'),
                password: env('POSTGRESQL_WRITES_PASSWORD'),
                connectionSize: (int)$connectionSize,
                applicationName: 'WRITESCONNECTOR'
            );
        }
    }

    public function getConn(): PostgreSQL
    {
        return self::$pool->getConn();
    }

    public function putConn(PostgreSQL $conn): void
    {
        self::$pool->putConn($conn);
    }
}
