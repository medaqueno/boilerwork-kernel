#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Clients;

use Kernel\Helpers\Singleton;

final class PostgreSQLWritesPool extends AbstractPostgreSQLPool
{
    use Singleton;

    protected readonly \Swoole\Coroutine\Channel $pool;

    /**
     * PostgresqlPool constructor.
     */
    public function __construct()
    {
        $host = $_ENV['POSTGRESQL_WRITES_HOST'] ?? 'postgres';
        $port = $_ENV['POSTGRESQL_WRITES_PORT'] ?? 5432;
        $dbname = $_ENV['POSTGRESQL_WRITES_DBNAME'] ?? 'test_event_sourcing';
        $username = $_ENV['POSTGRESQL_WRITES_USERNAME'] ?? 'postgres';
        $password = $_ENV['POSTGRESQL_WRITES_PASSWORD'] ?? 'postgres';

        // $size = ;
        $size = (int)(($_ENV['POSTGRESQL_SIZE_CONN'] ?? 64) / swoole_cpu_num()); // Will open a pool per swoole worker

        $this->fillPool($host, $port, $dbname, $username, $password, $size);

        echo "\nWrites Pool created\n";
    }
}
