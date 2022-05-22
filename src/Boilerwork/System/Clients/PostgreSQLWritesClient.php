#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Clients;

class PostgreSQLWritesClient extends AbstractPostgreSQLClient
{
    protected readonly AbstractPostgreSQLPool $pool;

    public function __construct()
    {
        $this->pool = \Boilerwork\System\Container\Container::getInstance()->get(PostgreSQLWritesPool::class);
    }
}
