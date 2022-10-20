#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Connectors\Sql;

use Boilerwork\Persistence\Pools\PostgreSQLReadsPool;
use Boilerwork\Support\Singleton;
use Swoole\Coroutine\PostgreSQL;

final class SqlReadsConnector
{
    use Singleton;

    public static ?PostgreSQLReadsPool $pool = null;

    private function __construct()
    {
        $this->initConn();
    }

    public function initConn()
    {
        if (self::$pool === null) {

            self::$pool = new PostgreSQLReadsPool();
            $connectionSize = (int)swoole_cpu_num(); // Will open a channel per swoole worker

            self::$pool->initPool(
                host: env('POSTGRESQL_READS_HOST'),
                port: (int)env('POSTGRESQL_READS_PORT'),
                dbname: env('POSTGRESQL_READS_DBNAME'),
                username: env('POSTGRESQL_READS_USERNAME'),
                password: env('POSTGRESQL_READS_PASSWORD'),
                connectionSize: (int)$connectionSize,
                applicationName: 'ReadsCONNECTOR'
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
