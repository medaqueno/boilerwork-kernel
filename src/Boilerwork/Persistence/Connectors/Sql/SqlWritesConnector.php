#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Connectors\Sql;

final class SqlWritesConnector extends AbstractSqlConnector
{
    public function __construct()
    {
        $connectionSize = (int)((env('POSTGRESQL_SIZE_CONN') ?? 12) / swoole_cpu_num()); // Will open a pool per swoole worker

        $this->initConnectionPool(
            host: env('POSTGRESQL_WRITES_HOST'),
            port: (int)env('POSTGRESQL_WRITES_PORT'),
            dbname: env('POSTGRESQL_WRITES_DBNAME'),
            username: env('POSTGRESQL_WRITES_USERNAME'),
            password: env('POSTGRESQL_WRITES_PASSWORD'),
            connectionSize: (int)$connectionSize,
        );
    }
}
