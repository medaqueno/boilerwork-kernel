#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql;

use Boilerwork\Persistence\Connectors\Sql\SqlWritesConnector;
use Boilerwork\Persistence\QueryBuilder\Sql\SqlQueryBuilder;

final class SqlWritesRepository extends AbstractSqlRepository
{
    public SqlWritesConnector $sqlConnector;

    public function __construct(
        protected readonly SqlQueryBuilder $queryBuilder,
    ) {
        $this->sqlConnector = SqlWritesConnector::getInstance();
    }
}
