#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Doctrine;

use Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits\Criteria;
use Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits\Query;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

final class DoctrineQueryBuilder
{
    use Query, Criteria;

    private QueryBuilder $queryBuilder;

    private Connection $conn;

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
    public function bindValues(array $params = []): self
    {
        return $this->setParameters($params);
    }

    public function setParameters(array $params = []): self
    {
        $this->queryBuilder = $this->queryBuilder->setParameters($params);
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

    public function fetchAll(): array
    {
        return $this->fetchAllAssociative();
    }

    public function fetchAllAssociative(): array
    {
        return $this->queryBuilder->fetchAllAssociative();
    }

    public function fetchOne(): mixed
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

    // public function addPaging(int $offset = 0, int $limit = 2): self
    // {
    //     $this->queryBuilder = $this->queryBuilder
    //         ->setFirstResult($offset)
    //         ->setMaxResults($limit);

    //     return $this;
    // }


}
