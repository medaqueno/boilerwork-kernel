#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\Adapters\PostgreSQL;

use Boilerwork\Helpers\Singleton;

final class PostgreSQLWritesPool extends AbstractPostgreSQLPool
{
    use Singleton;

    protected readonly \Swoole\Coroutine\Channel $pool;

    /**
     * PostgresqlPool constructor.
     */
    public function __construct()
    {
        $host = env('POSTGRESQL_WRITES_HOST') ?? 'postgres';
        $port = env('POSTGRESQL_WRITES_PORT') ?? 5432;
        $dbname = env('POSTGRESQL_WRITES_DBNAME') ?? 'test_event_sourcing';
        $username = env('POSTGRESQL_WRITES_USERNAME') ?? 'postgres';
        $password = env('POSTGRESQL_WRITES_PASSWORD') ?? 'postgres';

        // $size = ;
        $size = (int)((env('POSTGRESQL_SIZE_CONN') ?? 64) / swoole_cpu_num()); // Will open a pool per swoole worker

        $this->fillPool($host, $port, $dbname, $username, $password, $size);

        echo "\nWrites Pool created\n";
    }
}
