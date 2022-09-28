#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Connectors;

final class SqlReadsConnector extends AbstractSqlConnector
{
    public function __construct()
    {
        $connectionSize = (int)((env('POSTGRESQL_SIZE_CONN') ?? 12) / swoole_cpu_num()); // Will open a pool per swoole worker

        $this->initConnectionPool(
            host: env('POSTGRESQL_READS_HOST'),
            port: (int)env('POSTGRESQL_READS_PORT'),
            dbname: env('POSTGRESQL_READS_DBNAME'),
            username: env('POSTGRESQL_READS_USERNAME'),
            password: env('POSTGRESQL_READS_PASSWORD'),
            connectionSize: (int)$connectionSize,
        );
    }
}
