#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\Adapters\PostgreSQL;

class PostgreSQLReadsClient extends AbstractPostgreSQLClient
{
    protected readonly AbstractPostgreSQLPool $pool;

    public function __construct()
    {
        $this->pool = container()->get(PostgreSQLReadsPool::class);
    }
}
