#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql;

use Boilerwork\Persistence\Repositories\Sql\Traits\BuildPaging;
use Boilerwork\Persistence\Repositories\Sql\Traits\BuildQuery;
use Boilerwork\Persistence\Repositories\Sql\Traits\PrepareQuery;
use Boilerwork\Persistence\Repositories\Sql\Traits\Transactions;

abstract class AbstractSqlRepository
{
    use BuildQuery, BuildPaging, PrepareQuery, Transactions;

    /*******
     * RETRIEVE / EXECUTE QUERY
     *******/

    public function fetchAll(string $statement, array $bindValues = []): array
    {
        if ($this->queryBuilder->isPagingEnabled() === true) {
            $statement = $this->addPaging();
        }

        $statement = $this->prepareQuery($statement, $bindValues);
        return $this->sqlConnector->conn->fetchAll($statement);
    }

    public function fetchOne(string $statement, $bindValues): ?array
    {
        $statement = $this->prepareQuery($statement, $bindValues);
        return $this->sqlConnector->conn->fetchAssoc($statement) ?: null;
    }

    public function execute(string $statement, $bindValues): void
    {
        $this->prepareQuery($statement, $bindValues);
    }
}
