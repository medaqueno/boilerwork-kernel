#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits;

use Boilerwork\Persistence\QueryBuilder\CriteriaDto;
use Doctrine\DBAL\Query\QueryBuilder;

trait Criteria
{
    public function addCriteria(CriteriaDto $criteriaDto): self
    {
        $filterBy = array_filter($criteriaDto->params); // Remove null values

        if ($filterBy) {
            $this->addCriteriaFilter($filterBy);
        }

        if ($criteriaDto->orderBy) {

            $this->addCriteriaOrderBy($criteriaDto->orderBy);
        }
        return $this;
    }

    private function addCriteriaFilter(array $filterBy): QueryBuilder
    {
        foreach ($filterBy as $key => $value) {
            $this->queryBuilder
                ->andWhere($key . ' = :criteria_' . $key)
                ->setParameter('criteria_' . $key, $value);
        }

        return $this->queryBuilder;
    }

    public function addJsonCriteria(CriteriaDto $criteriaDto): self
    {
        $filterBy = array_filter($criteriaDto->params); // Remove null values

        if ($filterBy) {
            $this->addJsonCriteriaFilter($filterBy);
        }

        if ($criteriaDto->orderBy) {
            $this->addCriteriaOrderBy($criteriaDto->orderBy);
        }
        return $this;
    }

    private function addJsonCriteriaFilter(array $filterBy): QueryBuilder
    {
        foreach ($filterBy as $key => $value) {
            $this->queryBuilder
                ->andWhere(
                    sprintf(
                        "jsonb_path_exists(data, '$.**.%s ? (@ == \"%s\")')",
                        $key,
                        $value,
                    )
                );
        }

        return $this->queryBuilder;
    }

    private function addCriteriaOrderBy(string $orderBy): QueryBuilder
    {
        $orderParsed = explode(',', $orderBy);
        return $this->queryBuilder->addOrderBy(sort: $orderParsed[0], order: $orderParsed[1]);
    }
}
