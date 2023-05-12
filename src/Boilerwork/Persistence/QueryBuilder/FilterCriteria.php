#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\QueryBuilder;

use Boilerwork\Persistence\Exceptions\PagingException;

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

        $this->filteredData = $this->applyPostFilter($this->filteredData, $transformedFilter);

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
            $internalSortParam = $this->resolveInternalParameterName($sorting['sort']);

            if ($internalSortParam) {
                $order = $sorting['operator'] ?? 'asc';
                $this->filteredData = $this->applyOrderBy($this->filteredData, $internalSortParam, $order);
            }
        }

        return $this;
    }
    
    public function paginate(?array $paging): self
    {
        if ($paging) {
            $this->filteredData = $this->applyPaginate($this->filteredData, $paging['page'], $paging['per_page']);
        }

        return $this;
    }

    public function getResults(): array
    {
        return $this->filteredData;
    }

    private function applyPostFilter(array $results, array $postFilter): array
    {
        foreach ($postFilter as $attribute => $conditions) {
            $results = array_filter($results, function ($item) use ($attribute, $conditions) {
                if (is_array($conditions)) {
                    $conditionMet = false;
                    foreach ($conditions as $condition) {
                        if ($this->evaluateCondition($item, $attribute, '=', $condition)) {
                            $conditionMet = true;
                            break;
                        }
                    }
                    if ( ! $conditionMet) {
                        return false;
                    }
                } elseif (is_bool($conditions) || is_int($conditions) || is_float($conditions)) {
                    if ( ! $this->evaluateCondition($item, $attribute, '===', $conditions)) {
                        return false;
                    }
                } else {
                    if (
                        is_string($conditions) && ! str_contains($conditions, "-") && ! str_contains(
                            $conditions,
                            "≥",
                        ) && ! str_contains($conditions, "≤")
                    ) {
                        if ( ! $this->evaluateCondition($item, $attribute, '=', $conditions)) {
                            return false;
                        }
                    } else {
                        [$min, $operator, $max] = $this->parseRangeCondition($conditions);
                        if ($operator !== null) {
                            if ($operator === '-') {
                                if ($item[$attribute] < $min || $item[$attribute] > $max) {
                                    return false;
                                }
                            } elseif ($operator === '≥') {
                                if ($item[$attribute] < $min) {
                                    return false;
                                }
                            } elseif ($operator === '≤') {
                                if ($item[$attribute] > $min) {
                                    return false;
                                }
                            }
                        }
                    }
                }

                return true;
            });
        }

        return array_values($results);
    }

    private function applyOrderBy(array $results, string $attribute, string $order = 'asc'): array
    {
        usort($results, function ($a, $b) use ($attribute, $order) {
            $aValue = $this->getNestedValue($a, $attribute);
            $bValue = $this->getNestedValue($b, $attribute);

            if ($aValue == $bValue) {
                return 0;
            }

            if ($order === 'asc') {
                return ($aValue < $bValue) ? -1 : 1;
            } else {
                return ($aValue > $bValue) ? -1 : 1;
            }
        });

        return $results;
    }

    private function applyPaginate(array $results, int $page, int $perPage): array
    {
        $totalResults = count($results);
        $totalPages   = ceil($totalResults / $perPage);

        $pagingDto = new PagingDto(perPage: $perPage, page: $page);
        $pagingDto->setTotalCount($totalResults);

        if ($totalResults === 0) {
            return [];
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

        return array_slice($results, $start, $perPage);
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
                $currentValue = array_column($currentValue, $key);
            } else {
                if ( ! is_array($currentValue)) {
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

                if ( ! empty($tempArray)) {
                    $currentValue = $tempArray;
                } else {
                    return null;
                }
            }
        }

        return $currentValue;
    }

    protected function parseRangeCondition(string $condition): array
    {
        if (preg_match('/^(≥|≤)?(\d+)(-)?(\d+)?$/', $condition, $matches)) {
            $operator = $matches[1] ?: $matches[3];
            $min      = (int)$matches[2];
            $max      = isset($matches[4]) ? (int)$matches[4] : null;

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

    private function getUniqueValues(array $data, string $attribute, array $conditions): array
    {
        $values          = [];
        $displayValueKey = $conditions['displayValue'] ?? null;

        foreach ($data as $item) {
            $value = $this->getNestedValue($item, $attribute);

            if ($displayValueKey) {
                $displayValue = $this->getNestedValue($item, $displayValueKey);
            }

            if (is_array($value)) {
                foreach ($value as $subValue) {
                    $values[] = $displayValueKey ? ['id' => $subValue, 'name' => $displayValue] : $subValue;
                }
            } else {
                $values[] = $displayValueKey ? ['id' => $value, 'name' => $displayValue] : $value;
            }
        }

        // Remove duplicate arrays in values
        $values = array_map("unserialize", array_unique(array_map("serialize", $values)));
        sort($values);
        return array_values($values);
    }

    private function resolveInternalParameterName($externalParameter): ?string
    {
        return array_keys( array_filter($this->postFilter,
            fn ($filterParam) => $filterParam['external'] === $externalParameter
        ), )[0] ?? null;
    }
}
