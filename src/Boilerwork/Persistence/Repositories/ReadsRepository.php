#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories;

use Boilerwork\Persistence\Repositories\Sql\Doctrine\DoctrineQueryBuilder;

final readonly class ReadsRepository
{
    public DoctrineQueryBuilder $queryBuilder;

    /**
     * Injected configuration from Container
     *
     * @param array{host: string, port: int, dbname: string, user: string, password: string, poolsize: int} $connectionParams
     */
    public function __construct(
        array $connectionParams,
    ) {
        $this->queryBuilder = new DoctrineQueryBuilder($connectionParams);
    }
}
