#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories;

use Boilerwork\Persistence\Repositories\Sql\Doctrine\DoctrineQueryBuilder;

final readonly class MastersRepository
{
    public DoctrineQueryBuilder $queryBuilder;

    public function __construct()
    {
        $connectionParams = [
            'host'     => (string)env('POSTGRESQL_MASTERS_READS_HOST'),
            'port'     => (int)env('POSTGRESQL_MASTERS_READS_PORT'),
            'dbname'   => (string)env('POSTGRESQL_MASTERS_READS_DBNAME'),
            'user'     => (string)env('POSTGRESQL_MASTERS_READS_USERNAME'),
            'password' => (string)env('POSTGRESQL_MASTERS_READS_PASSWORD'),
            'poolsize' => 1,
        ];
        $this->queryBuilder = new DoctrineQueryBuilder($connectionParams);
    }
}
