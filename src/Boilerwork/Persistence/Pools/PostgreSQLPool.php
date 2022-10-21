#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Pools;

use Swoole\Coroutine\PostgreSQL;

abstract class PostgreSQLPool
{
    abstract public function initPool(
        string $host,
        int $port,
        string $dbname,
        string $username,
        string $password,
        int $connectionSize = 1,
        string $applicationName = 'AppService',
    ): void;

    protected function createConnection($host, $port, $dbname, $username, $password, $applicationName): PostgreSQL
    {
        $postgresql = new PostgreSQL();

        $res = $postgresql->connect(sprintf("host=%s;port=%s;dbname=%s;user=%s;password=%s;", $host, $port, $dbname, $username, $password, $applicationName));
        // $res = $postgresql->connect(sprintf("host=%s;port=%s;dbname=%s;user=%s;password=%s;options=--application_name=%s", $host, $port, $dbname, $username, $password, $applicationName));
        if ($res === false) {
            error('Failed to connect PostgreSQL server.');
            throw new \RuntimeException("Failed to connect PostgreSQL server.");
        }

        return $postgresql;
    }

    public function getConn(): PostgreSQL
    {
        return $this->pool->pop();
    }

    public function putConn(PostgreSQL $postgreSQL): void
    {
        $this->pool->push($postgreSQL);
    }

    public function close(): void
    {
        $this->pool->close();
        $this->pool = null;
    }
}
