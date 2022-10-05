#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Pools;

use Swoole\Coroutine\Channel;
use Swoole\Coroutine\PostgreSQL;

final class PostgreSQLPool
{
    // private static ?\Swoole\Coroutine\Channel $pool = null;

    private static ?PostgreSQL $conn = null;

    public function __construct(
        string $host,
        int $port,
        string $dbname,
        string $username,
        string $password,
        int $connectionSize,
    ) {
        $this->fillPool($host, $port, $dbname, $username, $password, $connectionSize);
    }

    /**
     * PostgresqlPool constructor.
     */
    private function fillPool($host, $port, $dbname, $username, $password, $connectionSize): void
    {
        if (self::$conn !== null) {
            return;
        }

        self::$conn = new PostgreSQL();

        $res = self::$conn->connect(sprintf("host=%s;port=%s;dbname=%s;user=%s;password=%s", $host, $port, $dbname, $username, $password));

        if ($res === false) {
            error('Failed to connect PostgreSQL server.');
            throw new \RuntimeException("Failed to connect PostgreSQL server.");
        }

        echo sprintf("\nPostgres Pool created: %s.%s - %s connections opened\n", $host, $dbname, '');
    }

    public function getConn(): PostgreSQL
    {
        return self::$conn;
    }

    public function putConn(PostgreSQL $postgreSQL): void
    {
        self::$conn = null;
    }

    public function close(): void
    {
    }
}
