#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql;

use Boilerwork\Persistence\Connectors\Sql\SqlReadsConnector;
use Boilerwork\Persistence\QueryBuilder\Sql\SqlQueryBuilder;
use Swoole\Coroutine\PostgreSQL;

final class SqlReadsRepository extends AbstractSqlRepository
{
    public static $counter = 0;
    // protected ?PostgreSQL $conn = null;
    public SqlReadsConnector $sqlConnector;
    public function __construct(
        protected readonly SqlQueryBuilder $queryBuilder,
        // protected readonly SqlReadsConnector $sqlConnector,
    ) {
        $this->sqlConnector = SqlReadsConnector::getInstance();
        var_dump(__CLASS__, ++self::$counter);
        // $this->conn = $this->sqlConnector->getConn();
    }
}
