#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories;

use Boilerwork\Persistence\Repositories\Sql\Doctrine\DoctrineQueryBuilder;

final class ReadsRepository
{
    public readonly DoctrineQueryBuilder $queryBuilder;

    /**
     * Injected configuration from Container
     *
     * @param array{host: string, port: int, dbname: string, user: string, password: string, poolsize: int} $connectionParams
     */
    public function __construct(
        private array $connectionParams,
    ) {
        $this->queryBuilder = new DoctrineQueryBuilder($connectionParams);
    }
}
