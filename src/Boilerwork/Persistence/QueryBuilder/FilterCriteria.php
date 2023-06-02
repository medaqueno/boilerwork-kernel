#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\QueryBuilder;

use Boilerwork\Http\QueryCriteria;
use Boilerwork\Persistence\Exceptions\PagingException;
use Ds\Set;
use Ds\PriorityQueue;

class FilterCriteria
{
    private array $filteredData = [];
    private array $postFilter = [];

    public function setData(array $data): self
    {
        $this->filteredData = $data;

        return $this;
    }

    public function postFilter(array $postFilter): self
    {
        $this->postFilter  = $postFilter;
        $transformedFilter = $this->transformFilter($postFilter);

        // No necesitamos reasignar el resultado a $this->filteredData
        $this->applyPostFilter($transformedFilter);

        return $this;
    }


    private function transformFilter(array $filterData): array
    {
        $transformedFilter = [];

        foreach ($filterData as $key => $data) {
            if (isset($data['value'])) {
                $transformedFilter[$key] = $data['value'];
            }
        }

        return $transformedFilter;
    }

    public function orderBy(?array $sorting): self
    {
        if ($sorting) {
            $order = $sorting['operator'] ?? 'asc';
            $this->applyOrderBy($sorting['sort'], $order);
        }

        return $this;
    }

    public function paginate(?array $paging): self
    {
        if ($paging) {
            $this->applyPaginate($paging['page'], $paging['per_page']);
        }

        return $this;
    }

    public function getResults(): array
    {
        return $this->filteredData;
    }

    private function applyPostFilter(array $postFilter): void
    {
        foreach ($postFilter as $attribute => $conditions) {
            $this->filteredData = array_filter($this->filteredData, function ($item) use ($attribute, $conditions) {
                if (is_array($conditions)) {
                    $conditionMet = false;
                    foreach ($conditions as $condition) {
                        if ($this->evaluateCondition($item, $attribute, '=', $condition)) {
                            $conditionMet = true;
                            break;
                        }
                    }
                    if (! $conditionMet) {
                        return false;
                    }
                } elseif (is_bool($conditions) || is_int($conditions) || is_float($conditions)) {
                    if (! $this->evaluateCondition($item, $attribute, '===', $conditions)) {
                        return false;
                    }
                } else {
                    if (
                        is_string($conditions) && ! str_contains($conditions, "-") && ! str_contains(
                            $conditions,
                            "≥",
                        ) && ! str_contains($conditions, "≤")
                    ) {
                        if (! $this->evaluateCondition($item, $attribute, '=', $conditions)) {
                            return false;
                        }
                    } else {
                        [$min, $operator, $max] = $this->parseRangeCondition($conditions);

                        if ($operator !== null) {
                            $valor = $this->getNestedValue($item, $attribute);

                            if ($operator === '-') {
                                if ($valor < $min || $valor > $max) {
                                    return false;
                                }
                            } elseif ($operator === '≥') {
                                if ($valor < $min) {
                                    return false;
                                }
                            } elseif ($operator === '≤') {
                                if ($valor > $max) {
                                    return false;
                                }
                            }
                        }
                    }
                }

                return true;
            });

            // Reset the array keys
            $this->filteredData = array_values($this->filteredData);
        }
    }

    private function applyOrderBy(string $attribute, string $order = 'asc'): void
    {
        $queue = new \Ds\PriorityQueue();

        foreach ($this->filteredData as $data) {
            $value = $this->getNestedValue($data, $attribute);

            $priority = match (gettype($value)) {
                'NULL' => $order === 'asc' ? PHP_INT_MAX : PHP_INT_MIN,
                'integer', 'double', 'boolean' => $order === 'asc' ? -(float)$value : (float)$value,
                'string' => $order === 'asc' ? 255 - ord($value) : ord($value),
                default => throw new \InvalidArgumentException(
                    'The attribute value must be a number, a boolean, a string, or null.'
                ),
            };

            $queue->push($data, $priority);
        }

        $this->filteredData = [];
        while (! $queue->isEmpty()) {
            $this->filteredData[] = $queue->pop();
        }
    }

    private function applyPaginate(int $page, int $perPage): void
    {
        if ($perPage < 1) {
            throw new PagingException(
                'pagination.invalidPageRequestPerPage',
                sprintf(
                    'PerPage must an integer greater than 0',
                ),
                400
            );
        }

        $totalResults = count($this->filteredData);
        $totalPages   = ceil($totalResults / $perPage);

        $pagingDto = new PagingDto(perPage: $perPage, page: $page);
        $pagingDto->setTotalCount($totalResults);

        if ($totalResults === 0) {
            $this->filteredData = [];

            return;
        }

        if ($page < 1 || $page > $totalPages) {
            throw new PagingException(
                'pagination.invalidPageRequest',
                sprintf(
                    'Page requested: %u is not valid. Total pages: %u',
                    $pagingDto->page(),
                    $pagingDto->totalPages(),
                ),
                400
            );
        }

        $start = ($page - 1) * $perPage;

        $this->filteredData = array_slice($this->filteredData, $start, $perPage);
    }

    public function evaluateCondition($row, $column, $operator, $value): bool
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

    private function getNestedValue(array $array, string $key): mixed
    {
        $keys         = explode('.', $key);
        $currentValue = $array;

        foreach ($keys as $key) {
            if (is_array($currentValue) && isset($currentValue[$key])) {
                $currentValue = $currentValue[$key];
            } elseif (is_array($currentValue) && array_key_exists($key, $currentValue)) {
                return $currentValue[$key];
            } else {
                if (! is_array($currentValue)) {
                    throw new \InvalidArgumentException(
                        "Invalid path provided: '$key' not found in the nested structure."
                    );
                }

                $tempArray = [];

                foreach ($currentValue as $item) {
                    if (is_array($item) && isset($item[$key])) {
                        $tempArray[] = $item[$key];
                    }
                }

                if (! empty($tempArray)) {
                    $currentValue = $tempArray;
                } else {
                    return null;
                }
            }
        }

        // Si el valor es un array con un solo elemento, se extrae ese elemento.
        if (is_array($currentValue) && count($currentValue) === 1) {
            $currentValue = reset($currentValue);
        }
        
        return $currentValue;
    }


    protected function parseRangeCondition(string $condition): array
    {
        // Reconoce rangos de integer y float
        if (preg_match('/^(≥|≤)?([\d\.]+)(-)?([\d\.]+)?$/', $condition, $matches)) {
            $operator = $matches[1] ?: $matches[3];
            $min      = $matches[2];
            $max      = $matches[4] ?? null;

            // Cuando el operador es '≤', estamos estableciendo un máximo, no un mínimo.
            if ($operator === '≤') {
                $max = $min;
                $min = null;
            }

            return [$min, $operator, $max];
        }

        return [null, null, null];
    }


    public function getMetaFilters(array $originalData): array
    {
        $metaFilters = [];

        foreach ($this->postFilter as $attribute => $conditions) {
            $externalAttribute               = $conditions['external'];
            $metaFilters[$externalAttribute] = $this->getUniqueValues($originalData, $attribute, $conditions);
        }

        return $metaFilters;
    }

    private function generateValues(array $data, string $attribute, array $conditions): \Generator
    {
        $displayValueKey = $conditions['displayValue'] ?? null;

        foreach ($data as $item) {
            $value = $this->getNestedValue($item, $attribute);

            if ($displayValueKey) {
                $displayValue = $this->getNestedValue($item, $displayValueKey);
            }

            if (is_array($value)) {
                foreach ($value as $subValue) {
                    yield $displayValueKey ? ['id' => $subValue, 'name' => $displayValue] : $subValue;
                }
            } else {
                yield $displayValueKey ? ['id' => $value, 'name' => $displayValue] : $value;
            }
        }
    }

    private function getUniqueValues(array $data, string $attribute, array $conditions): array
    {
        $valuesGenerator = $this->generateValues($data, $attribute, $conditions);

        $uniqueValues = new Set();
        foreach ($valuesGenerator as $value) {
            $uniqueValues->add($value);
        }

        $uniqueValuesArray = $uniqueValues->toArray();

        // Check if condition is a range
        if (is_string($conditions['value']) && str_contains($conditions['value'], '-')) {
            $minValue = min($uniqueValuesArray);
            $maxValue = max($uniqueValuesArray);

            return [$minValue, $maxValue];
        }

        sort($uniqueValuesArray);

        return $uniqueValuesArray;
    }

    private function resolveInternalParameterName($externalParameter): ?string
    {
        return array_keys(
            array_filter(
                $this->postFilter,
                fn($filterParam) => $filterParam['external'] === $externalParameter,
            ),
        )[0] ?? null;
    }
}
