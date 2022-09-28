#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories;

use Boilerwork\Persistence\Repositories\Traits\BuildPaging;
use Boilerwork\Persistence\Repositories\Traits\BuildQuery;
use Boilerwork\Persistence\Repositories\Traits\PrepareQuery;
use Boilerwork\Persistence\Repositories\Traits\Transactions;

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
