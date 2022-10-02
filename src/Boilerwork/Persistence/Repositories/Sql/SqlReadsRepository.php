#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql;

use Boilerwork\Persistence\Connectors\Sql\SqlReadsConnector;
use Boilerwork\Persistence\QueryBuilder\Sql\SqlQueryBuilder;

final class SqlReadsRepository extends AbstractSqlRepository
{
    public function __construct(
        protected readonly SqlQueryBuilder $queryBuilder,
        protected readonly SqlReadsConnector $sqlConnector,
    ) {
    }
}
