#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Traits;

use Boilerwork\Persistence\Exceptions\PersistenceException;

trait PrepareQuery
{
    /*******
     * Prepare query
     *******/

    // https://stackoverflow.com/questions/27908977/how-to-use-parametric-order-by-with-pg-prepare-pg-execute
    private function prepareQuery(string $statement, array $bindValues = []): mixed
    {
        $newStatement = $this->parseStatementForSwooleClient(
            originalStatement: $statement,
            bindValues: $bindValues,
        );

        $queryName = (string)(md5($newStatement));
        $this->sqlConnector->conn->prepare($queryName, $newStatement);
        $result = $this->sqlConnector->conn->execute($queryName, $bindValues);

        if ($this->sqlConnector->conn->resultDiag !== null) {
            $this->checkError($result);
        }

        return $result;
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

    private function checkError()
    {
        $resultDiag = $this->sqlConnector->conn->resultDiag;

        // May be a handled error
        error(
            sprintf('DB error/warning: severity: %s, sqlstate: %s, table_name: %s, message_primary: %s, message_detail: %s, constraint_name: %s', $resultDiag['severity'], $resultDiag['sqlstate'], $resultDiag['table_name'], $resultDiag['message_primary'], $resultDiag['message_detail'], $resultDiag['constraint_name'])
        );

        match ($resultDiag['sqlstate']) {
            '23505' => throw new PersistenceException('Duplicate key value violates unique constraint', 409),
            default => throw new PersistenceException('Error committing db query', 500),
        };
    }
}
