#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits;

trait Query
{
    public function addSelect(...$columns): self
    {
        $this->queryBuilder =  $this->connector->conn->createQueryBuilder()
            ->addSelect(...$columns);

        return $this;
    }

    /**
     *
     * @example: ->from('foo')           // table name
     * @example: ->from('bar', 'b');     // alias the table as desired
     */
    public function from(string $table, string $alias = null): self
    {
        $this->queryBuilder = $this->queryBuilder->from($table, $alias);
        return $this;
    }

    /**
     * @example: ->where('bar > :bar')
     * @example: ->where('foo = :foo')
     * @example: ->where('bar IN (:bar)')
     */
    public function where(string $condition): self
    {
        $this->queryBuilder = $this->queryBuilder->where($condition);
        return $this;
    }

    /**
     * @example: ->andWhere('bar > :bar')
     * @example: ->andWhere('foo = :foo')
     * @example: ->andWhere('bar IN (:bar)')
     */
    public function andWhere(string $condition): self
    {
        $this->queryBuilder = $this->queryBuilder->andWhere($condition);
        return $this;
    }

    /**
     * @example: ->orWhere('bar > :bar')
     */
    public function orWhere(string $condition): self
    {
        $this->queryBuilder = $this->queryBuilder->orWhere($condition);
        return $this;
    }

    /**
     * @example:  ->having('users > 10')
     */
    public function having(string $cond): self
    {
        $this->queryBuilder->having($cond);
        return $this;
    }

    /**
     * @example:  ->orHaving('users > 10')
     */
    public function orHaving(string $cond): self
    {
        $this->queryBuilder->orHaving($cond);
        return $this;
    }

    /**
     * @example: ->andHaving('users > 10')
     */
    public function andHaving(string $cond): self
    {
        $this->queryBuilder->andHaving($cond);
        return $this;
    }

    /**
     * @example: ->groupBy('DATE(last_login)')
     */
    public function groupBy(string $groupBy): self
    {
        $this->queryBuilder->groupBy($groupBy);
        return $this;
    }

    /**
     * @example: ->groupBy('DATE(last_login)')
     */
    public function addGroupBy(string $groupBy): self
    {
        $this->queryBuilder->addGroupBy($groupBy);
        return $this;
    }

    /**
     * Shorthand for innerJoin
     * @example: ->select('u.id', 'u.name', 'p.number')
     *           ->from('users', 'u')
     *           ->join('u', 'phonenumbers', 'p', 'u.id = p.user_id')
     */
    public function join(string $joinType, string $joinToTable, string $cond): self
    {
        $this->queryBuilder->innerJoin($joinType, $joinToTable, $cond);
        return $this;
    }

    /**
     *
     * @example: ->select('u.id', 'u.name', 'p.number')
     *           ->from('users', 'u')
     *           ->innerJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id')
     */
    public function innerJoin(string $joinType, string $joinToTable, string $cond): self
    {
        $this->queryBuilder->innerJoin($joinType, $joinToTable, $cond);
        return $this;
    }

    /**
     *
     * @example: ->select('u.id', 'u.name', 'p.number')
     *           ->from('users', 'u')
     *           ->leftJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id')
     */
    public function leftJoin(string $joinType, string $joinToTable, string $cond): self
    {
        $this->queryBuilder->leftJoin($joinType, $joinToTable, $cond);
        return $this;
    }

    /**
     *
     * @example: ->select('u.id', 'u.name', 'p.number')
     *           ->from('users', 'u')
     *           ->rightJoin('u', 'phonenumbers', 'p', 'u.id = p.user_id')
     */
    public function rightJoin(string $joinType, string $joinToTable, string $cond): self
    {
        $this->queryBuilder->rightJoin($joinType, $joinToTable, $cond);
        return $this;
    }

    /**
     * @example: ->orderBy('baz', 'ASC')
     */
    public function orderBy(string $sort, string $order = null): self
    {
        $this->queryBuilder->orderBy($sort, $order);
        return $this;
    }

    /**
     * @example: ->addOrderBy('baz', 'ASC')
     */
    public function addOrderBy(string $sort, string $order = null): self
    {
        $this->queryBuilder->addOrderBy($sort, $order);
        return $this;
    }

    /**
     * @example: ->limit(2)
     */
    public function limit(?int $limit): self
    {
        $this->queryBuilder->setMaxResults($limit);
        return $this;
    }

    /**
     * @example: ->offset(2)
     */
    public function offset(int $offset): self
    {
        $this->queryBuilder->setFirstResult($offset);
        return $this;
    }

    public function distinct(): self
    {
        $this->queryBuilder->distinct();
        return $this;
    }
}
