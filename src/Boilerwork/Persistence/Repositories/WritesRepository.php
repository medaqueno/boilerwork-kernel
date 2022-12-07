#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories;

use Boilerwork\Persistence\Repositories\Sql\Doctrine\DoctrineQueryBuilder;

final class WritesRepository
{
    public readonly DoctrineQueryBuilder $queryBuilder;

    /**
     * Injected configuration from Container
     *
     * @param array{host: string, dbname: string, user: string, password: string, driver: string, maxConnections: ?int} $connectionParams
     */
    public function __construct(
        private array $connectionParams,
    ) {
        $this->queryBuilder = new DoctrineQueryBuilder($connectionParams);
    }
}
