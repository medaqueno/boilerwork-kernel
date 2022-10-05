#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql;

use Boilerwork\Persistence\Connectors\Sql\SqlWritesConnector;
use Boilerwork\Persistence\QueryBuilder\Sql\SqlQueryBuilder;

final class SqlWritesRepository extends AbstractSqlRepository
{
    public function __construct(
        protected readonly SqlQueryBuilder $queryBuilder,
        protected readonly SqlWritesConnector $sqlConnector,
    ) {
        if ($this->conn === null) {
            $this->conn = $this->sqlConnector->getConn();

            // Execute at the end of coroutine process
            \Swoole\Coroutine\defer(function () {
                $this->sqlConnector->putConn($this->conn);
            });
        }
    }
}
