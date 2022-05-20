#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Clients;

use Boilerwork\Helpers\Singleton;

final class PostgreSQLReadsPool extends AbstractPostgreSQLPool
{
    use Singleton;

    protected readonly \Swoole\Coroutine\Channel $pool;

    /**
     * PostgresqlPool constructor.
     */
    public function __construct()
    {
        $host = $_ENV['POSTGRESQL_READS_HOST'] ?? 'postgres';
        $port = $_ENV['POSTGRESQL_READS_PORT'] ?? 5432;
        $dbname = $_ENV['POSTGRESQL_READS_DBNAME'] ?? 'read_projections';
        $username = $_ENV['POSTGRESQL_READS_USERNAME'] ?? 'postgres';
        $password = $_ENV['POSTGRESQL_READS_PASSWORD'] ?? 'postgres';

        // $size = ;
        $size = (int)(($_ENV['POSTGRESQL_SIZE_CONN'] ?? 32) / swoole_cpu_num()); // Will open a pool per swoole worker

        $this->fillPool($host, $port, $dbname, $username, $password, $size);

        echo "\nReads Pool created\n";
    }
}
