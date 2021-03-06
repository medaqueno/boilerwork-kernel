#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\Adapters\PostgreSQL;

use Boilerwork\Infra\Persistence\Exceptions\PersistenceException;
use Swoole\Coroutine\PostgreSQL;

/**
 * Client to PostgreSQL
 *
 * Get a connection from connection pool in order to work with.
 * Don't forget to put the connection back to the pool
 *
 * @see https://openswoole.com/docs/modules/swoole-coroutine-postgres
 * Official documentation with all existing methods.
 *
 * Offical Error codes: https://www.postgresql.org/docs/current/errcodes-appendix.html
 *
 * @example
        $pgClient = new PostgreSQLClient();
        $pgClient->getConnection();
        $arr = $pgClient->fetchAll('select * from events where "aggregateId" = $1', ["1e1ec9be-2c19-48c5-9580-5de4088cbcf6"]);
        var_dump($arr);
        $pgClient->putConnection();
 *
 **/
class AbstractPostgreSQLClient
{
    private PostgreSQL $conn;

    protected readonly AbstractPostgreSQLPool $pool;

    /**
     * Retrieve connection in order to query DB
     **/
    public function getConnection(): void
    {
        $this->conn = $this->pool->getConn();
    }
    /**
     * Put connection back to the pool in order to be reused
     **/
    public function putConnection(): void
    {
        $this->pool->putConn($this->conn);
    }

    /**
     * @description Run Query, and prepare/execute it automatically if includes args
     * @example $query = $pgClient->run('select * from events where "aggregateId" = $1', ["1e1ec9be-2c19-48c5-9580-5de4088cbcf6"]);
     * @throws \Swoole\Exception Throw error if something happen with DB
     **/
    public function run(string $query, array $args = [])
    {
        if (!$args) {
            $result = $this->query($query);
        } else {
            $queryName = $this->prepare($query);
            $result = $this->execute($queryName, $args);
        }

        if ($this->conn->resultDiag !== null) {
            $this->checkError($result);
        }

        return $result;
    }

    private function query(string $query): mixed
    {
        $result = $this->conn->query($query);

        if ($this->conn->resultDiag !== null) {
            $this->checkError($result);
        }

        return $result;
    }

    public function fetchAll(string $query, array $args = []): array
    {
        $result = $this->run($query, $args);
        return $this->conn->fetchAll($result);
        // $resp = [];
        // while ($row = $this->conn->fetchRow($result)) {
        //     $resp[] = $row;
        // }

        // return $resp;
    }

    public function fetchOne(string $query, array $args = []): ?array
    {
        $result = $this->run($query, $args);
        return $this->conn->fetchAssoc($result) ?: null;
    }

    private function prepare(string $query): string
    {
        $queryName = (string)(uniqid());
        $this->conn->prepare($queryName, $query);

        return $queryName;
    }

    private function execute(string $queryName, array $values)
    {
        return $this->conn->execute($queryName, $values);
    }

    public function initTransaction(): void
    {
        $this->conn->query('BEGIN');
    }

    public function endTransaction(): void
    {
        $this->conn->query('COMMIT');
    }

    public function status()
    {
        return $this->conn->status();
    }

    private function checkError($result = null)
    {
        $resultDiag = $this->conn->resultDiag;
        // $resultStatus = $this->conn->resultStatus;

        var_dump($resultDiag);

        // May be a handled error
        // error(
        //     sprintf('DB error/warning: severity: %s, sqlstate: %s, table_name: %s, message_primary: %s, message_detail: %s, constraint_name: %s', $resultDiag['severity'], $resultDiag['sqlstate'], $resultDiag['table_name'], $resultDiag['message_primary'], $resultDiag['message_detail'], $resultDiag['constraint_name'])
        // );

        match ($resultDiag['sqlstate']) {
            '23505' => throw new PersistenceException('Duplicate key value violates unique constraint', 409),
            default => throw new PersistenceException('Error committing db query', 500),
        };
    }
}
