#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Connectors;

use Boilerwork\Persistence\Pools\PostgreSQLPool;
use Swoole\Coroutine\PostgreSQL;

abstract class AbstractSqlConnector
{
    public ?PostgreSQL $conn;

    protected PostgreSQLPool $pool;

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

        $this->getConn();

        // Execute at the end of coroutine process
        \Swoole\Coroutine\defer(function () {
            $this->putConn();
        });
    }

    public function getConn(): void
    {
        $this->conn = $this->pool->getConn();
    }

    public function putConn(): void
    {
        $this->pool->putConn($this->conn);
        $this->conn = null;
    }
}
