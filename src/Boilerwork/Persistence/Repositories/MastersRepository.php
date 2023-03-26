#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories;

use Boilerwork\Persistence\Repositories\Sql\Doctrine\DoctrineQueryBuilder;

use function env;
use function var_dump;

final class MastersRepository
{
    public readonly DoctrineQueryBuilder $queryBuilder;

    public function __construct()
    {
        $connectionParams = [
            'host' => env('POSTGRESQL_MASTERS_READS_HOST'),
            'dbname' => env('POSTGRESQL_MASTERS_READS_DBNAME'),
            'user' => env('POSTGRESQL_MASTERS_READS_USERNAME'),
            'password' => env('POSTGRESQL_MASTERS_READS_PASSWORD'),
            'driver' => 'pdo_pgsql',
            'maxConnections' => env('POSTGRESQL_MASTERS_SIZE_CONN'),
        ];
        $this->queryBuilder = new DoctrineQueryBuilder($connectionParams);
    }
}
