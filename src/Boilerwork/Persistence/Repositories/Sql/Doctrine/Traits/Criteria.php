#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits;

use Boilerwork\Http\QueryCriteria;
use Boilerwork\Persistence\QueryBuilder\CriteriaDto;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

use function is_string;
use function sprintf;

trait Criteria
{
    public function addQueryCriteria(QueryCriteria $queryCriteria): self
    {
        $whereParams = $queryCriteria->getSearchParams();

        if (count($whereParams) > 0) {
            $this->addQueryCriteriaFilter($whereParams);
        }

        if ($queryCriteria->getSortingParam()) {
            $this->addCriteriaOrderBy($queryCriteria->getSortingParam());
        }

        return $this;
    }

    private function addQueryCriteriaFilter(array $whereParams): QueryBuilder
    {

        foreach ($whereParams as $key => $value) {
            $value = $value['value'];
            $valueType = gettype($value);

            match (gettype($value)) {
                'boolean' => $this->queryBuilder
                    ->andWhere($key . ' = :criteria_' . $key)
                    ->setParameter('criteria_' . $key, $value, ParameterType::BOOLEAN),
                'integer' => $this->queryBuilder
                    ->andWhere($key . ' = :criteria_' . $key)
                    ->setParameter('criteria_' . $key, $value, ParameterType::INTEGER),
                'NULL' => $this->queryBuilder
                    ->andWhere($key . ' IS NULL'),
                default => $this->queryBuilder
                    ->andWhere(
                        sprintf(
                            'lower(immutable_unaccent(%s::TEXT)) = lower(immutable_unaccent(:criteria_%s))',
                            $key,
                            $key,
                        ),
                    )
                    ->setParameter('criteria_' . $key, (string)$value, ParameterType::STRING)
            };
        }
        return $this->queryBuilder;
    }

    private function addCriteriaOrderBy(array $orderBy): QueryBuilder
    {
        if (is_string($orderBy['sort'])) {
            return $this->queryBuilder->addOrderBy(
                sort : sprintf(
                    '(lower(immutable_unaccent (%s::TEXT)))',
                    $orderBy['sort'],
                ),
                order: $orderBy['operator'],
            );
        }

        return $this->queryBuilder->addOrderBy(sort: sprintf('%s', $orderBy['sort']), order: $orderBy['operator']);
    }



    public function addJsonQueryCriteria(QueryCriteria $queryCriteria): self
    {
        if (count($queryCriteria->getSearchParams()) > 0) {
            $this->addJsonQueryCriteriaFilter($queryCriteria->getSearchParams());
        }

        if ($queryCriteria->getSortingParam()) {
            $this->addJsonQueryCriteriaOrderBy($queryCriteria->getSortingParam());
        }

        return $this;
    }

    private function addJsonQueryCriteriaFilter(array $whereParams): QueryBuilder
    {
        $index = 0;
        foreach ($whereParams as $key => $value) {
            $jsonPath = explode('.', $key);
            $valuePlaceholder = ":value{$index}";

            if (is_bool($value)) {
                $valuePlaceholder = $value ? 'true' : 'false';
            } elseif (is_int($value) || is_float($value)) {
                $valuePlaceholder = $value;
            } elseif (is_null($value) || $value === 'null') {
                $value = null;
                $valuePlaceholder = 'NULL';
            }

            $columnName = $jsonPath[0];

            if (count($jsonPath) > 2) {
                array_shift($jsonPath);
                $lastPathPart = array_pop($jsonPath);
                $jsonPathExpression = implode('->', array_map(function ($pathPart) {
                    return sprintf("'%s'", $pathPart);
                }, $jsonPath));

                $this->queryBuilder
                    ->andWhere(
                        sprintf(
                            "%s %s %s",
                            is_string($value) ? "lower(immutable_unaccent(" : "",
                            "{$columnName} -> {$jsonPathExpression} ->> '{$lastPathPart}'",
                            is_string($value) ? "))" : ""
                        )
                        . ($value === null ? 'IS' : '=')
                        . sprintf(
                            " %s%s%s",
                            is_string($value) ? "lower(immutable_unaccent(" : "",
                            $valuePlaceholder,
                            is_string($value) ? "))" : ""
                        )
                    );

                if (is_string($value)) {
                    $this->queryBuilder->setParameter("value{$index}", $value);
                }

            } else {
                $this->queryBuilder
                    ->andWhere(
                        sprintf(
                            "%s %s %s",
                            is_string($value) ? "lower(immutable_unaccent(" : "",
                            "{$columnName} ->> '{$jsonPath[1]}'",
                            is_string($value) ? "))" : ""
                        )
                        . ($value === null ? 'IS' : '=')
                        . sprintf(
                            " %s%s%s",
                            is_string($value) ? "lower(immutable_unaccent(" : "",
                            $valuePlaceholder,
                            is_string($value) ? "))" : ""
                        )
                    );

                if (is_string($value)) {
                    $this->queryBuilder->setParameter("value{$index}", $value);
                }
            }
            $index++;
        }

        return $this->queryBuilder;
    }

    private function addJsonQueryCriteriaOrderBy(array $orderBy): QueryBuilder
    {
        if (is_string($orderBy['sort'])) {
            $jsonPath = explode('.', $orderBy['sort']);
            if (count($jsonPath) > 2) {
                $columnName = array_shift($jsonPath);
                $lastPathPart = array_pop($jsonPath);
                $jsonPathExpression = implode(
                    '->',
                    array_map(function ($pathPart) {
                        return sprintf("'%s'", $pathPart);
                    }, $jsonPath)
                );

                return $this->queryBuilder->addOrderBy(
                    sort: sprintf(
                        "lower(immutable_unaccent((%s -> %s ->> '%s')::TEXT))",
                        $columnName,
                        $jsonPathExpression,
                        $lastPathPart
                    ),
                    order: $orderBy['operator']
                );
            } else {
                $columnName = $jsonPath[0];

                return $this->queryBuilder->addOrderBy(
                    sort: sprintf(
                        "lower(immutable_unaccent((%s ->> '%s')::TEXT))",
                        $columnName,
                        $jsonPath[1]
                    ),
                    order: $orderBy['operator']
                );
            }
        }

        return $this->queryBuilder->addOrderBy(
            sort: sprintf('%s', $orderBy['sort']),
            order: $orderBy['operator']
        );
    }




    /*
     * @deprecated
     * */
    public function addCriteria(CriteriaDto $criteriaDto): self
    {
        $filterBy = array_filter($criteriaDto->params()); // Remove null values

        if ($filterBy) {
            $this->addCriteriaFilter($filterBy);
        }

        if ($criteriaDto->orderBy()) {
            $this->addCriteriaOrderBy($criteriaDto->orderBy());
        }

        return $this;
    }


    private function addCriteriaFilter(array $filterBy): QueryBuilder
    {
        foreach ($filterBy as $key => $value) {
            if (is_string($value)) {
                $this->queryBuilder
                    ->andWhere(
                        sprintf(
                            'lower(immutable_unaccent(%s::TEXT)) = lower(immutable_unaccent(:criteria_%s))',
                            $key,
                            $key,
                        ),
                    )
                    ->setParameter(sprintf('criteria_%s', $key), $value);
            } else {
                $this->queryBuilder
                    ->andWhere($key . ' = :criteria_' . $key)
                    ->setParameter('criteria_' . $key, $value);
            }
        }

        return $this->queryBuilder;
    }





    /*
     * @deprecated
     * */
    public function addJsonCriteria(CriteriaDto $criteriaDto): self
    {
        $filterBy = array_filter($criteriaDto->params()); // Remove null values

        if ($filterBy) {
            $this->addJsonCriteriaFilter($filterBy);
        }

        if ($criteriaDto->orderBy()) {
            $this->addCriteriaOrderBy($criteriaDto->orderBy());
        }

        return $this;
    }

    private function addJsonCriteriaFilter(array $filterBy): QueryBuilder
    {
        foreach ($filterBy as $key => $value) {
            $this->queryBuilder
                ->andWhere(
                // "jsonb_path_exists(data, '$.**.%s ? (@ == \"%s\")')",
                    sprintf(
                        "lower(immutable_unaccent (data -> %s)) = lower(immutable_unaccent ('%s'))",
                        $key,
                        $value,
                    ),
                );
        }

        return $this->queryBuilder;
    }
}
