#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\QueryBuilder;

use Aura\SqlQuery\Common\Delete;
use Aura\SqlQuery\Common\Insert;
use Aura\SqlQuery\Common\Select;
use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\Common\Update;
use Aura\SqlQuery\Exception;
use Boilerwork\Infra\Persistence\Adapters\PostgreSQL\AbstractPostgreSQLPool;
use Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLReadsPool;
use Boilerwork\Infra\Persistence\Exceptions\PersistenceException;
use Swoole\Coroutine\PostgreSQL;

/**
 * Wrap Aura SQLQUERY
 * @see: https://github.com/auraphp/Aura.SqlQuery
 */
final class SqlQueryBuilder implements QueryBuilderInterface
{
    private Select|Insert|Update|Delete $query;

    private readonly AbstractPostgreSQLPool $pool;
    private ?PostgreSQL $conn;

    public function __construct(
        // return Common, not SQLite-specific, query objects
        private readonly QueryFactory $queryFactory = new QueryFactory('sqlite', QueryFactory::COMMON),
    ) {
        $this->pool = globalContainer()->get(PostgreSQLReadsPool::class);
    }

    /**
     * @return ?array{optional?: mixed}
     */
    public function fetchOne(): ?array
    {
        $statement = $this->prepareQuery($this->getStatement(), $this->getBindValues());
        return $this->conn->fetchAssoc($statement) ?: null;
    }

    private const DEFAULT_ROWS_PER_PAGE = 5;

    /**
     * @return array{optional?: mixed}[]
     */
    public function fetchAll(): array
    {
        $this->addPaging();

        $statement = $this->prepareQuery($this->getStatement(), $this->getBindValues());
        return $this->conn->fetchAll($statement);
    }

    /**
     * @return ?array{optional?: mixed}
     *
     * @example: ->fetchOneFromRaw('SELECT * FROM table_name WHERE field = $1', ['value']);
     * @example: ->fetchOneFromRaw('SELECT * FROM table_name LIMIT $1', [1]);
     */
    public function fetchOneFromRaw(string $rawQuery, array $bindValues = []): mixed
    {
        $statement = $this->prepareQuery($rawQuery, $bindValues);
        return $this->conn->fetchAssoc($statement) ?: null;
    }

    /**
     * @return array{optional?: mixed}[]
     *
     * @example: ->fetchAllFromRaw('SELECT * FROM table_name');
     * @example: ->fetchAllFromRaw('SELECT * FROM table_name WHERE field = $1 LIMIT $2', ['value',10]);
     */
    public function fetchAllFromRaw(string $rawQuery, array $bindValues = []): mixed
    {
        $this->addPaging();

        $statement = $this->prepareQuery($rawQuery, $bindValues);
        return $this->conn->fetchAll($statement);
    }

    /**
     * Execute queries for Write statements
     */
    public function execute(): void
    {
        $this->prepareQuery($this->getStatement(), $this->getBindValues());
    }

    private function prepareQuery(string $statement, array $bindValues = []): mixed
    {
        $newStatement = $this->parseStatementForSwooleClient(
            originalStatement: $statement,
            bindValues: $bindValues,
        );

        // Execute at the end of coroutine process
        \Swoole\Coroutine\defer(function () {
            $this->pool->putConn($this->conn);
            $this->conn = null;
        });
        $this->conn = $this->pool->getConn();

        $queryName = (string)(uniqid());
        $this->conn->prepare($queryName, $newStatement);

        $result = $this->conn->execute($queryName, array_values($bindValues));

        if ($this->conn->resultDiag !== null) {
            $this->checkError($result);
        }

        return $result;
    }

    private function addPaging(): void
    {
        if (!container()->has('Paging')) {
            return;
        }

        $pagingContainer = container()->get('Paging');

        $perPage = $pagingContainer->perPage() ?? self::DEFAULT_ROWS_PER_PAGE;
        $page = $pagingContainer->page() ?? 1;

        // Always before set paging and page to current query
        $pagingContainer->setTotalCount($this->retrieveTotalCount());

        $this->query->setPaging($perPage);
        $this->query->page($page);
    }

    /**
     * Clone current query,
     * and manipulate it to only retrieve total number of records
     * to be added to pagination metadata response
     */
    private function retrieveTotalCount(): int
    {
        $countQuery = new self;
        $cloneCurrent = clone $this->query;

        $response = $countQuery->fetchOneFromRaw(
            $cloneCurrent->resetCols()->resetOrderBy()->cols(['count(*)'])->getStatement(),
            $cloneCurrent->getBindValues()
        );

        unset($countQuery, $cloneCurrent);

        return $response['count'] ?? 0;
    }

    private function checkError()
    {
        $resultDiag = $this->conn->resultDiag;

        // May be a handled error
        error(
            sprintf('DB error/warning: severity: %s, sqlstate: %s, table_name: %s, message_primary: %s, message_detail: %s, constraint_name: %s', $resultDiag['severity'], $resultDiag['sqlstate'], $resultDiag['table_name'], $resultDiag['message_primary'], $resultDiag['message_detail'], $resultDiag['constraint_name'])
        );

        match ($resultDiag['sqlstate']) {
            '23505' => throw new PersistenceException('Duplicate key value violates unique constraint', 409),
            default => throw new PersistenceException('Error committing db query', 500),
        };
    }

    public function initTransaction(): void
    {
        $this->conn->query('BEGIN');
    }

    public function endTransaction(): void
    {
        $this->conn->query('COMMIT');
    }


    /** */


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

    private function getBindValues(): array
    {
        return $this->query->getBindValues();
    }

    public function getStatement(): string
    {
        return $this->query->getStatement();
    }

    /**
     * Replace named binded values with incremental integers
     * Eg: :field_name -> $1
     * @param string $originalStatement
     * @return string
     */
    private function parseStatementForSwooleClient(string $originalStatement, $bindValues): string
    {
        $i = 1;
        $replacingValues = [];
        foreach ($bindValues as $key => $value) {
            $replacingValues[] =  '$' . $i++;
        }

        return str_replace(array_keys($bindValues), $replacingValues, $originalStatement);
    }

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
        $this->paging = $paging;
        $this->query->setPaging($paging);
        return $this;
    }

    public function getPaging(): int
    {
        return $this->query->getPaging();
    }
}
