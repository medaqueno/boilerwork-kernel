#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories;

use Boilerwork\Persistence\Connectors\SqlWritesConnector;
use Boilerwork\Persistence\QueryBuilder\SqlQueryBuilder;

final class SqlWritesRepository extends AbstractSqlRepository
{
    public function __construct(
        protected SqlQueryBuilder $queryBuilder,
        protected SqlWritesConnector $sqlConnector,
    ) {
    }
}
