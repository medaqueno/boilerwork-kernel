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

    protected ?PostgreSQL $conn = null;

    /*******
     * RETRIEVE / EXECUTE QUERY
     *******/

    public function fetchAll(string $statement, array $bindValues = []): array
    {
        if ($this->queryBuilder->isPagingEnabled() === true) {
            $statement = $this->addPaging();
        }

        $statement = $this->prepareQuery($statement, $bindValues);

        return $this->conn->fetchAll($statement);
    }

    public function fetchOne(string $statement, $bindValues): ?array
    {

        $statement = $this->prepareQuery($statement, $bindValues);
        return $this->conn->fetchAssoc($statement) ?: null;
    }

    public function execute(string $statement, $bindValues): void
    {
        $this->prepareQuery($statement, $bindValues);
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
