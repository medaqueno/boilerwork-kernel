#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Clients;

class PostgreSQLWritesClient extends AbstractPostgreSQLClient
{
    protected readonly AbstractPostgreSQLPool $pool;

    public function __construct()
    {
        $this->pool = app()->container()->get(PostgreSQLWritesPool::class);
    }
}
