#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Traits;

use Boilerwork\Persistence\QueryBuilder\Sql\Paging;

trait BuildPaging
{
    private function addPaging(): string
    {
        if (!container()->has('Paging')) {
            new Paging();
        }

        $pagingContainer = container()->get('Paging');

        // Always before set paging and page to current query
        $pagingContainer->setTotalCount($this->retrieveTotalCount());

        $this->queryBuilder->setPaging($pagingContainer->perPage());
        $this->queryBuilder->page($pagingContainer->page());

        return $this->queryBuilder->getStatement();
    }

    /**
     * Clone current query,
     * and manipulate it to only retrieve total number of records
     * to be added to pagination metadata response
     *
     * Re-uses the connection already opened by "parent" query.
     */
    private function retrieveTotalCount(): int
    {
        $cloneCurrent = clone $this->queryBuilder->query;
        $statement =  $cloneCurrent->resetCols()->resetOrderBy()->cols(['count(*)'])->getStatement();

        $conn = $this->sqlConnector->getConn();
        $statement = $this->prepareQuery($conn, $statement, $cloneCurrent->getBindValues());

        $response =  $conn->fetchAssoc($statement) ?: null;
        $this->sqlConnector->putConn($conn);

        unset($cloneCurrent, $conn);

        return $response['count'] ?? 0;
    }
}
