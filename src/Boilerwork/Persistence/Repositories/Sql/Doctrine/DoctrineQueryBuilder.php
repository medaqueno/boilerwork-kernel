#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Doctrine;

use Boilerwork\Persistence\Exceptions\PagingException;
use Boilerwork\Persistence\QueryBuilder\PagingDto;
use Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits\Criteria;
use Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits\CriteriaExtended;
use Boilerwork\Support\ValueObjects\Language\Language;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

use Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits\Autocomplete;

final class DoctrineQueryBuilder
{
    use Criteria;
    use CriteriaExtended;
    use Autocomplete;

    private QueryBuilder $queryBuilder;

    private Connection $conn;

    private const DEFAULT_LIMIT = 25;

    /**
     * Injected configuration from Container
     *
     * @param array{host: string, dbname: string, user: string, password: string, driver: string} $connectionParams
     */
    public function __construct(
        private array $connectionParams
    ) {
        $this->conn = DriverManager::getConnection($connectionParams);
    }

    /**
     * @example: ->select('id','title')                      // column name
     * @example: ->select('name AS namecol')          // one way of aliasing
     * @example: ->select('DATE(last_login) as date', 'COUNT(id) AS users')   // embed calculations directly
     */
    public function select(...$columns): self
    {
        $this->queryBuilder = $this->conn->createQueryBuilder()
            ->select(...$columns);
        return $this;
    }


    public function update(string $table, string $alias = null): self
    {
        $this->queryBuilder = $this->conn->createQueryBuilder()
            ->update($table, $alias);
        return $this;
    }

    public function insert(string $table): self
    {
        $this->queryBuilder = $this->conn->createQueryBuilder()
            ->insert($table);
        return $this;
    }

    public function delete(string $table, ?string $alias = null): self
    {
        $this->queryBuilder = $this->conn->createQueryBuilder()
            ->delete($table, $alias);
        return $this;
    }

    /**
     * @deprecated Use raw method instead
     */
    public function selectFromRaw(string $sql, array $params = []): Result
    {
        return $this->raw($sql, $params);
    }

    public function raw(string $sql, array $params = []): Result
    {
        return $this->conn->executeQuery($sql, $params);
    }

    /**
     * @alias setParameters
     */
    public function bindValues(array $params = [], array $types = []): self
    {
        return $this->setParameters($params, $types);
    }

    /**
     * @param list<mixed>|array<string, mixed>                                     $params Parameters to set
     * @param array<int, int|string|Type|null>|array<string, int|string|Type|null> $types ParamsType
     */
    public function setParameters(array $params = [], array $types = []): self
    {
        $this->queryBuilder = $this->queryBuilder->setParameters($params, $types);
        return $this;
    }

    public function setParameter(int|string $param, mixed $value, mixed $type = null): self
    {
        $this->queryBuilder = $this->queryBuilder->setParameter($param, $value, $type);
        return $this;
    }

    public function values(array $values = []): self
    {
        $this->queryBuilder = $this->queryBuilder->values($values);
        return $this;
    }

    public function expr(): self
    {
        $this->queryBuilder = $this->queryBuilder->expr();
        return $this;
    }

    /**
     * NOT TESTED YET
     */
    public function in(string $x, string|array $y): self
    {
        $this->queryBuilder = $this->queryBuilder->expr()->in($x, $y);
        return $this;
    }

    /**
     *
     * @example: ->set('u.logins', 'u.logins + 1')
     * @example: ->set('logins', ':value')
     */
    public function set(string $column, ?string $value): self
    {
        $this->queryBuilder = $this->queryBuilder->set($column, $value);
        return $this;
    }

    /**
     * @example: ->setMultiLang('column_name', ':value')
     */
    public function setMultiLang(string $column, ?string $value): self
    {
        $this->queryBuilder = $this->queryBuilder
            ->set($column, sprintf('%s || :%s', $column, $column));
        return $this;
    }

    /**
     * Allow using all methods in implementation directly bypassing abstract wrapper.
     */
    public function connection(): Connection
    {
        return $this->conn;
    }

    public function initTransaction(): bool
    {
        return $this->conn->beginTransaction();
    }

    public function endTransaction(): bool
    {
        try {
            return $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Return all rows
     * @deprecated use fetchAllAssociative()
     */
    public function fetchAll(): array
    {
        return $this->fetchAllAssociative();
    }

    /**
     * Return all rows
     * If not LIMIT present, a default value is added: @see self::DEFAULT_LIMIT
     */
    public function fetchAllAssociative(): array
    {
        if (stripos($this->queryBuilder->getSQL(), 'LIMIT') === false) {
            $this->queryBuilder->setMaxResults(self::DEFAULT_LIMIT);
        }

        return $this->queryBuilder->fetchAllAssociative();
    }

    /**
     * Return the first row
     */
    public function fetchAssociative(): mixed
    {
        return $this->queryBuilder->fetchAssociative();
    }

    public function executeQuery(): iterable
    {
        $result = $this->queryBuilder->executeQuery();
    }

    /**
     * @deprecated use fetchAssociative()
     */
    public function fetchOne(): mixed
    {
        return $this->fetchAssociative();
    }

    /**
     * Return the value of a single column of the first row
     */
    public function fetchValue(): mixed
    {
        return $this->queryBuilder->fetchOne();
    }

    /**
     * Executes INSERT, UPDATE or DELETE
     */
    public function execute(): int
    {
        return $this->queryBuilder->executeStatement();
    }

    /**
     * @deprecated Use getSQL() method instead
     */
    public function getStatement(): string
    {
        return $this->getSQL();
    }

    public function getSQL(): string
    {
        return $this->queryBuilder->getSQL();
    }

    /**
     * @deprecated Use getParameters() method instead
     */
    public function getBindValues(): array
    {
        return $this->getParameters();
    }

    public function getParameters(): array
    {
        return $this->queryBuilder->getParameters();
    }


    // QUERY


    public function addSelect(...$columns): self
    {
        $this->queryBuilder = $this->conn->createQueryBuilder()
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
    // END QUERY

    private string $primaryColumn = 'id_primary';

    public function addPaging(): self
    {
        if (!container()->has('Paging')) {
            return $this;
        }

        /** @var \Boilerwork\Persistence\QueryBuilder\PagingDto */
        $pagingDto = container()->get('Paging');

        $from = $this->queryBuilder->getQueryPart('from');

        if (count($from) > 1) {
            var_dump("MUCHAS TABLAS");
            return $this;
        } else {
            $this->addPagingForOneTable(table: $from[0]['table'], pagingDto: $pagingDto);
        }

        return $this;
    }

    private function addPagingForOneTable(string $table, PagingDto $pagingDto): void
    {
        $countQuery = clone $this->queryBuilder;
        $countQuery->resetQueryParts(['select', 'orderBy']);
        $pagingDto->setTotalCount(
            $countQuery->addSelect('COUNT(id_primary)')->setMaxResults(1)->fetchOne()
        );

        // We have no results, so we return empty data set as fast as possible
        if ($pagingDto->totalCount() === 0) {
            $this->queryBuilder->resetQueryParts(['select', 'distinct', 'where', 'join', 'groupBy', 'having', 'orderBy'])
                ->select('1')
                ->setMaxResults(0);

            return;
        }

        if ($pagingDto->page() - 1 >= $pagingDto->totalPages()) {
            throw new PagingException(
                'pagination.invalidPageRequest',
                sprintf('Page requested: %u is not valid. Total pages: %u', $pagingDto->page(), $pagingDto->totalPages()),
                400
            );
        }

        $offset = $pagingDto->perPage() * ($pagingDto->page() - 1);
        $this->queryBuilder->setFirstResult($offset)->setMaxResults($pagingDto->perPage());
    }


    /**
     * @example: ->selectMultiLang('id', 'es', 'identity')
     */
    public function selectMultiLang(string $field, string $lang, string $alias): self
    {
        $select = sprintf('%s->>\'%s\'', $field, strtoupper($lang));

        if ($alias) {
            $select = sprintf('%s as %s', $select, $alias);
        }
        // Add fallfack lang
        $fallbackSelect = sprintf('%s->>\'%s\' as %s_fallback', $field, Language::FALLBACK, $alias);

        $this->queryBuilder
            ->addSelect($select)
            ->addSelect($fallbackSelect);

        return $this;
    }

    public function scopePublish(): self
    {
        $this->queryBuilder->andWhere('deleted_at is null');
        return $this;
    }
}
