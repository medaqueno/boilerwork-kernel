#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Traits;

use Boilerwork\Persistence\Exceptions\PersistenceException;

trait PrepareQuery
{
    /*******
     * Prepare query
     *******/

    // https://stackoverflow.com/questions/27908977/how-to-use-parametric-order-by-with-pg-prepare-pg-execute
    private function prepareQuery($conn, string $statement, array $bindValues = []): mixed
    {
        $newStatement = $this->parseStatementForSwooleClient(
            statement: $statement,
            bindValues: $bindValues,
        );

        $queryName = (string)(uniqid((string)random_int(1, 9999)));
        // $queryName = (string)(md5($newStatement));
        $conn->prepare($queryName, $newStatement);
        $result = $conn->execute($queryName, $bindValues);

        if ($conn->resultDiag !== null) {
            $this->checkError($conn);
        }

        return $result;
    }

    /**
     * Replace named binded values with incremental integers
     * Eg: :field_name -> $1
     * Eg: :field_name, -> $1,
     * @param string $statement
     * @return string
     */
    private function parseStatementForSwooleClient(string $statement, $bindValues): string
    {
        for ($i = 0; $i < count($bindValues); $i++) {

            // If binded variable is followed by a comma, include it in replace
            $comma = mb_strpos($statement, array_keys($bindValues)[$i] . ',') ? ',' : '';

            $statement = str_replace(
                sprintf('%s%s', array_keys($bindValues)[$i], $comma),
                sprintf('$%s%s', ($i + 1), $comma),
                $statement
            );
        }

        return $statement;
    }

    private function checkError($conn)
    {
        $resultDiag = $conn->resultDiag;

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
