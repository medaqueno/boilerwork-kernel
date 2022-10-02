#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Pools;

use Swoole\Coroutine\Channel;
use Swoole\Coroutine\PostgreSQL;

final class PostgreSQLPool
{
    private readonly \Swoole\Coroutine\Channel $pool;

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
        $this->pool = new Channel((int)$connectionSize);

        for ($i = 0; $i < $connectionSize; $i++) {
            $postgresql = new PostgreSQL();

            $res = $postgresql->connect(sprintf("host=%s;port=%s;dbname=%s;user=%s;password=%s", $host, $port, $dbname, $username, $password));

            if ($res === false) {
                error('Failed to connect PostgreSQL server.');
                throw new \RuntimeException("Failed to connect PostgreSQL server.");
            } else {
                $this->putConn($postgresql);
            }
        }

        echo sprintf("\nPostgres Pool created: %s.%s - %s connections opened\n", $host, $dbname,  $this->pool->capacity);
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
