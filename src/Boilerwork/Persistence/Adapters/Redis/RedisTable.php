#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Adapters\Redis;

/**
 * Class RedisTable
 *
 * Wrapper for the Redis adapter that implements methods to simulate queries/where in Redis as if using a table.
 */
final class RedisTable
{
    private array $conditions = [];
    private string $tableName;

    public function __construct(
        private readonly RedisAdapter $redis,
    )
    {
    }


    private function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    public function fromTable(string $tableName): self
    {
        $this->setTableName($tableName);

        return $this;
    }

    public function setTableExpire(int $ttl = 3600): bool
    {
        return $this->redis->expire($this->tableName, $ttl);
    }

    /**
     * Deletes a "table" from Redis.
     *
     * Usage example:
     *
     * $this->redisTable->fromTable('users')->deleteTable();
     */
    public function deleteTable(): int
    {
        return $this->redis->del($this->tableName);
    }

    /**
     * Inserts a record into the specified "table".
     *
     * @param array $data Data to be inserted.
     * @param bool $overwriteById Whether to overwrite the existing record with the same 'id'.
     *
     * @return bool True if the record was inserted or overwritten, false otherwise.
     *
     * Usage example:
     *
     * $this->redisTable->fromTable('users')->insert([
     *     'id' => 1,
     *     'name' => 'John Doe',
     *     'age' => 30,
     * ], true);
     */
    public function insert(array $data, bool $overwriteById = false): bool
    {
        $existingRecordIndex = null;

        if ($overwriteById && isset($data['id'])) {
            $existingRecordIndex = $this->getRecordIndexById($data['id']);
        }

        if ($existingRecordIndex !== null) {
            // Sobrescribir el registro existente
            $this->redis->lset($this->tableName, $existingRecordIndex, json_encode($data));
        } else {
            // Insertar un nuevo registro
            $this->redis->rpush($this->tableName, json_encode($data));
        }

        return true;
    }

    private function getRecordIndexById(string $id): int|null
    {
        $tableData = $this->redis->lrange($this->tableName, 0, -1);

        foreach ($tableData as $index => $json) {
            $row = json_decode($json, true);
            if (isset($row['id']) && $row['id'] === $id) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Adds a condition to filter results when selecting records.
     *
     * @param string $column Column name or nested path (e.g. 'address.city').
     * @param string $operator Comparison operator (e.g. '=', '!=', '>', '>=', '<', '<=').
     * @param mixed $value Value to compare against.
     *
     * @return RedisTable Instance of this class, allowing for method chaining.
     *
     * Usage example:
     *
     * $this->redisTable->where('age', '>', 30);
     */
    public function where(string $column, string $operator, mixed $value): self
    {
        $this->conditions[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Selects all records from the specified "table" that match the added conditions.
     *
     *
     * @return array Array of records that match the conditions.
     *
     * Usage example:
     *
     * $results = $this->redisTable->fromTable('users')->where('age', '>', 30)->selectAll();
     * print_r($results);
     */
    public function selectAll(): array
    {
        $tableData = $this->redis->lrange($this->tableName, 0, -1);
        $decodedData = array_map(function ($json) {
            return json_decode($json, true);
        }, $tableData);

        if (!empty($this->conditions)) {
            $decodedData = array_filter($decodedData, function ($row) {
                foreach ($this->conditions as $condition) {
                    $column = $condition['column'];
                    $operator = $condition['operator'];
                    $value = $condition['value'];
                    if (!$this->evaluateCondition($row, $column, $operator, $value)) {
                        return false;
                    }
                }

                return true;
            });
        }

        $this->conditions = [];

        return $decodedData;
    }

    /**
     * Retrieve all records from the given table in a paginated manner using a generator.
     * This method allows for efficient memory usage when processing large datasets, as it doesn't load all the data into memory at once.
     * It fetches and processes the data page by page, according to the specified page size.
     *
     * @param int $pageSize The number of records to fetch at a time (defaults to 100).
     *
     * @return \Generator Yields an associative array representing a single record that matches the specified conditions (if any).
     *
     * @example
     * // Retrieve all records from the 'users' table, processing 50 records at a time
     * $records = $this->redisTable->fromTable('users')->selectAllIterative(pagesize: 50);
     * foreach ($records as $record) {
     *     // Process the record (e.g., display, store in a file, etc.)
     * }
     *
     * // Or instead of foreach
     * print_r(iterator_to_array($records));
     *
     * @example
     * // Retrieve all records from the 'users' table where the 'age' attribute is greater than 30
     * $records = $this->redisTable->fromTable('users')->where('age', '>', 30)->selectAllIterative();
     * foreach ($records as $record) {
     *     // Process the record (e.g., display, store in a file, etc.)
     * }
     */
    public function selectAllIterative(int $pageSize = 100): \Generator
    {
        $startIndex = 0;
        while (true) {
            $tableData = $this->redis->lrange($this->tableName, $startIndex, $startIndex + $pageSize - 1);

            if (empty($tableData)) {
                break;
            }

            foreach ($tableData as $json) {
                $row = json_decode($json, true);

                if (!empty($this->conditions)) {
                    $passesConditions = true;
                    foreach ($this->conditions as $condition) {
                        $column = $condition['column'];
                        $operator = $condition['operator'];
                        $value = $condition['value'];

                        if (!$this->evaluateCondition($row, $column, $operator, $value)) {
                            $passesConditions = false;
                            break;
                        }
                    }

                    if ($passesConditions) {
                        yield $row;
                    }
                } else {
                    yield $row;
                }
            }

            $startIndex += $pageSize;
        }

        $this->conditions = [];
    }

    private function evaluateCondition($row, $column, $operator, $value): bool
    {
        $columnValue = $this->getNestedValue($row, $column);

        if (is_array($columnValue)) {
            $results = [];

            foreach ($columnValue as $item) {
                $results[] = $this->compareValues($item, $operator, $value);
            }

            return in_array(true, $results, true);
        }

        return $this->compareValues($columnValue, $operator, $value);
    }


    private function compareValues($columnValue, $operator, $value): bool
    {
        return match ($operator) {
            '=' => $columnValue == $value,
            '===' => $columnValue === $value,
            '!==' => $columnValue !== $value,
            '!=' => $columnValue != $value,
            '>' => $columnValue > $value,
            '>=' => $columnValue >= $value,
            '<' => $columnValue < $value,
            '<=' => $columnValue <= $value,
            default => throw new \InvalidArgumentException("Unsupported operator: $operator"),
        };
    }

    /**
     * Retrieves a value from a nested array using a key or a dot-separated path.
     *
     * @param array $array Array to search for the value.
     * @param string $key Key or dot-separated path (e.g. 'address.city').
     *
     * @return mixed|null The value if found, null otherwise.
     */
    private function getNestedValue(array $array, string $key): mixed
    {
        $keys = explode('.', $key);
        $currentValue = $array;

        foreach ($keys as $key) {
            if (is_array($currentValue) && isset($currentValue[$key])) {
                $currentValue = $currentValue[$key];
            } elseif (is_array($currentValue) && array_key_exists($key, $currentValue)) {
                $currentValue = array_column($currentValue, $key);
            } else {
                $tempArray = [];

                foreach ($currentValue as $item) {
                    if (is_array($item) && isset($item[$key])) {
                        $tempArray[] = $item[$key];
                    }
                }

                if (!empty($tempArray)) {
                    $currentValue = $tempArray;
                } else {
                    return null;
                }
            }
        }

        return $currentValue;
    }

    /**
     * Deletes a record by the specified id from the "table".
     *
     * @param string $id Index of the record to delete.
     *
     * Usage example:
     *
     * $this->redisTable->fromTable('users')->delete(0);
     */
    public function deleteById(string $id): void
    {
        // Obtener el índice del registro con el 'id' proporcionado
        $recordIndex = $this->getRecordIndexById($id);

        // Si el registro existe, eliminarlo
        if ($recordIndex !== null) {
            $this->delete($recordIndex);
        } else {
            throw new \Exception("No record found with id: $id");
        }
    }

    /**
     * Deletes a record at the specified index from the "table".
     *
     * @param int $index Index of the record to delete.
     *
     * Usage example:
     *
     * $this->redisTable->fromTable('users')->delete(0);
     */
    private function delete(int $index): void
    {
        $placeholder = "DELETED-" . uniqid();
        $this->redis->lSet($this->tableName, $index, $placeholder);
        $this->redis->lRem($this->tableName, $placeholder, 1);
    }
}
