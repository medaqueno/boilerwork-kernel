#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\QueryBuilder\Sql;

use Aura\SqlQuery\Common\Delete;
use Aura\SqlQuery\Common\Insert;
use Aura\SqlQuery\Common\Select;
use Aura\SqlQuery\Common\Update;
use Aura\SqlQuery\QueryFactory;

/**
 * Wrap Aura SQLQUERY
 * @see: https://github.com/auraphp/Aura.SqlQuery
 */
final class SqlQueryBuilder
{
    public Select|Insert|Update|Delete $query;

    public QueryFactory $queryFactory;

    public function __construct()
    {
        // return Common, not SQLite-specific, query objects
        $this->queryFactory = new QueryFactory('sqlite', QueryFactory::COMMON);
    }

    /**
     *
     * @example: 'id',                       // column name
     * @example: 'name AS namecol',          // one way of aliasing
     * @example: 'col_name' => 'col_alias',  // another way of aliasing
     * @example: 'COUNT(foo) AS foo_count'   // embed calculations directly
     */
    public function select(array $cols = ['*']): self
    {
        $this->query = $this->queryFactory->newSelect()->cols($cols);
        return $this;
    }

    /**
     *
     * @example: ->from('foo')           // table name
     * @example: ->from('bar AS b');     // alias the table as desired
     */
    public function from(string $table): self
    {
        $this->query->from($table);
        return $this;
    }

    /**
     * @example: ->fromSubSelect('SELECT ...', 'my_sub');
     */
    public function fromSubSelect(string $select, string $alias = 'my_sub'): self
    {
        $this->query->fromSubSelect($select, $alias);
        return $this;
    }

    /**
     *
     * @example: 'id',                       // column name
     * @example: 'name AS namecol',          // one way of aliasing
     * @example: 'col_name' => 'col_alias',  // another way of aliasing
     */
    public function update(array $cols = []): self
    {
        $this->query = $this->queryFactory->newUpdate()->cols($cols);
        return $this;
    }

    /**
     * @todo
     */
    public function set(string $col, string $value): self
    {
        $this->query->set($col, filter_var($value, FILTER_SANITIZE_ADD_SLASHES));
        return $this;
    }

    /**
     * @example: ->table('foo')           // table name
     */
    public function table(string $table): self
    {
        $this->query->table($table);
        return $this;
    }

    /**
     * @example: ->where('bar > :bar')
     * @example: ->where('foo = :foo')
     * @example: ->where('bar IN (:bar)')
     */
    public function where($cond): self
    {
        $this->query->where($cond);
        return $this;
    }

    /**
     * @example: ->orWhere('bar > :bar')
     */
    public function orWhere($cond): self
    {
        $this->query->orWhere($cond);
        return $this;
    }

    /**
     * @example: ->orderBy(['baz ASC'])
     */
    public function orderBy(array $orderBy): self
    {
        $this->query->orderBy($orderBy);
        return $this;
    }

    /**
     * @example: ->limit(2)
     */
    public function limit(int $limit): self
    {
        $this->query->limit($limit);
        return $this;
    }

    /**
     * @example: ->offset(2)
     */
    public function offset(int $offset): self
    {
        $this->query->offset($offset);
        return $this;
    }

    public function having(string $cond): self
    {
        $this->query->having($cond);
        return $this;
    }

    public function orHaving(string $cond): self
    {
        $this->query->orHaving($cond);
        return $this;
    }

    public function groupBy(array $cols): self
    {
        $this->query->groupBy($cols);
        return $this;
    }

    public function join(string $joinType, string $joinToTable, string $cond): self
    {
        $this->query->join($joinType, $joinToTable, $cond);
        return $this;
    }

    public function joinSubSelect(string $joinType, string $subSelectToJoinOn, string $asName, string $onCond): self
    {
        $this->query->joinSubSelect($joinType, $subSelectToJoinOn, $asName, $onCond);
        return $this;
    }

    public function distinct(): self
    {
        $this->query->distinct();
        return $this;
    }

    public function union(): self
    {
        $this->query->union();
        return $this;
    }

    public function unionAll(): self
    {
        $this->query->unionAll();
        return $this;
    }

    /**
     *
     * @example: 'id',                       // column name
     * @example: 'name AS namecol',          // one way of aliasing
     * @example: 'col_name' => 'col_alias',  // another way of aliasing
     */
    public function insert(array $cols = ['*']): self
    {
        $this->query = $this->queryFactory->newInsert()->cols($cols);
        return $this;
    }

    /**
     * @example: ->into('foo')           // table name
     */
    public function into(string $table): self
    {
        $this->query->into($table);
        return $this;
    }

    public function delete(): self
    {
        $this->query = $this->queryFactory->newDelete();
        return $this;
    }

    /**
     * @example: ->bindValues([':fieldname' => 'ValueExample', ':otherfieldname' => 'FooValue']);
     */
    public function bindValues(array $values): self
    {
        $this->query->bindValues($values);
        return $this;
    }

    public function getBindValues(): array
    {
        return $this->query->getBindValues();
    }

    public function getStatement(): string
    {
        return $this->query->getStatement();
    }

    /**
     * Add Paging to query methods
     */
    public function page($page): self
    {
        $this->query->page($page);
        return $this;
    }

    public function getPage(): int
    {
        return $this->query->getPage();
    }

    public function setPaging($paging): self
    {
        $this->query->setPaging($paging);
        return $this;
    }

    public function getPaging(): int
    {
        return $this->query->getPaging();
    }

    private bool $pagingEnabled = false;

    public function isPagingEnabled(): bool
    {
        return $this->pagingEnabled;
    }

    public function addPaging(): self
    {
        $this->pagingEnabled = true;
        return $this;
    }
}
