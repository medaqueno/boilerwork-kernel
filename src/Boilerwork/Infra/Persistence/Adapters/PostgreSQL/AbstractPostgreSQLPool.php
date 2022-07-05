#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\Adapters\PostgreSQL;

use Swoole\Coroutine\Channel;
use Swoole\Coroutine\PostgreSQL;

class AbstractPostgreSQLPool
{
    protected readonly \Swoole\Coroutine\Channel $pool;

    /**
     * PostgresqlPool constructor.
     */
    // public function __construct()
    // {
    //     $host = $_ENV['POSTGRESQL_HOST'] ?? 'postgres';
    //     $port = $_ENV['POSTGRESQL_PORT'] ?? 5432;
    //     $dbname = $_ENV['POSTGRESQL_DBNAME'] ?? 'test_event_sourcing';
    //     $username = $_ENV['POSTGRESQL_USERNAME'] ?? 'postgres';
    //     $password = $_ENV['POSTGRESQL_PASSWORD'] ?? 'postgres';

    //     // $size = ;
    //     $size = (int)(($_ENV['POSTGRESQL_SIZE_CONN'] ?? 64) / swoole_cpu_num()); // Will open a pool per swoole worker

    //     $this->fillPool($host, $port, $dbname, $username, $password, $size);
    // }

    protected function fillPool($host, $port, $dbname, $username, $password, $size): void
    {
        $this->pool = new Channel((int)$size);

        for ($i = 0; $i < $size; $i++) {
            $postgresql = new PostgreSQL();

            $res = $postgresql->connect(sprintf("host=%s;port=%s;dbname=%s;user=%s;password=%s", $host, $port, $dbname, $username, $password));

            if ($res === false) {
                // error('failed to connect PostgreSQL server.');
                echo 'failed to connect PostgreSQL server.';
                var_dump($res);
                throw new \RuntimeException("failed to connect PostgreSQL server.");
            } else {
                $this->putConn($postgresql);
            }
        }

        echo "POSTGRESQL POOL CREATED. " . $this->pool->capacity . " connections opened\n";
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
