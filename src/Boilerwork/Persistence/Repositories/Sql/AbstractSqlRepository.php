#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql;

use Boilerwork\Persistence\Repositories\Sql\Traits\BuildPaging;
use Boilerwork\Persistence\Repositories\Sql\Traits\BuildQuery;
use Boilerwork\Persistence\Repositories\Sql\Traits\PrepareQuery;
use Boilerwork\Persistence\Repositories\Sql\Traits\Transactions;
use Swoole\Coroutine\PostgreSQL;

abstract class AbstractSqlRepository
{
    use BuildQuery, BuildPaging, PrepareQuery, Transactions;

    /*******
     * RETRIEVE / EXECUTE QUERY
     *******/

    public function fetchAll(string $statement, array $bindValues = []): array
    {
        $conn = $this->sqlConnector->getConn();

        if ($this->queryBuilder->isPagingEnabled() === true) {
            $statement = $this->addPaging();
        }

        $statement = $this->prepareQuery($conn, $statement, $bindValues);

        $res = $conn->fetchAll($statement);

        $this->sqlConnector->putConn($conn);
        unset($conn);

        return $res;
    }

    public function fetchOne(string $statement, $bindValues): ?array
    {
        $conn = $this->sqlConnector->getConn();

        $statement = $this->prepareQuery($conn, $statement, $bindValues);
        $res =  $conn->fetchAssoc($statement) ?: null;

        $this->sqlConnector->putConn($conn);
        unset($conn);

        return $res;
    }

    public function execute(string $statement, $bindValues): void
    {
        $conn = $this->sqlConnector->getConn();

        $this->prepareQuery($conn, $statement, $bindValues);

        $this->sqlConnector->putConn($conn);
        unset($conn);
    }

    /**
     * Convert boolean value into Postgres booleans"
     * Postgres needs 'y' or 'f' value in boolean types
     */
    public function convertBoolean(bool $boolean): string
    {
        return $boolean === true ? 'y' : 'n';
    }
}
