#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Traits;

use Boilerwork\Persistence\QueryBuilder\SqlQueryBuilder;

trait BuildQuery
{

    /*******
     * BUILD QUERY
     *******/

    /**
     *
     * @example: 'id',                       // column name
     * @example: 'name AS namecol',          // one way of aliasing
     * @example: 'col_name' => 'col_alias',  // another way of aliasing
     * @example: 'COUNT(foo) AS foo_count'   // embed calculations directly
     */
    final public function select($cols = ['*']): SqlQueryBuilder
    {
        return $this->queryBuilder->select($cols);
    }

    final public function selectFromRaw(string $rawStatement): SqlQueryBuilder
    {
        return $this->select()->fromSubSelect($rawStatement);
    }

    /**
     *
     * @example: 'id',                       // column name
     * @example: 'name AS namecol',          // one way of aliasing
     * @example: 'col_name' => 'col_alias',  // another way of aliasing
     */
    final public function insert(array $cols = ['*']): SqlQueryBuilder
    {
        return $this->queryBuilder->insert($cols);
    }

    final public function delete(): SqlQueryBuilder
    {
        return $this->queryBuilder->delete();
    }

    /**
     *
     * @example: 'id',                       // column name
     * @example: 'name AS namecol',          // one way of aliasing
     * @example: 'col_name' => 'col_alias',  // another way of aliasing
     */
    final public function update(array $cols = []): SqlQueryBuilder
    {
        return $this->queryBuilder->update($cols);
    }
}
