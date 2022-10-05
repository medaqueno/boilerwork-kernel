#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Connectors\Sql;

use Boilerwork\Persistence\Pools\PostgreSQLPool;
use Swoole\Coroutine\PostgreSQL;

abstract class AbstractSqlConnector
{
    private readonly PostgreSQLPool $pool;

    protected function initConnectionPool(
        string $host,
        int $port,
        string $dbname,
        string $username,
        string $password,
        int $connectionSize,
    ) {
        $this->pool = new PostgreSQLPool(
            host: $host,
            port: $port,
            dbname: $dbname,
            username: $username,
            password: $password,
            connectionSize: $connectionSize,
        );
    }

    public function getConn(): PostgreSQL
    {
        return $this->pool->getConn();
    }

    public function putConn(PostgreSQL $conn): void
    {
        $this->pool->putConn($conn);
    }
}
