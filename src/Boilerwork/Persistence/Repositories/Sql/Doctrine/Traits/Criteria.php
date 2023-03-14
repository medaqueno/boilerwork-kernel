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
        $filterBy = array_filter($criteriaDto->params()); // Remove null values

        if ($filterBy) {
            $this->addCriteriaFilter($filterBy);
        }

        if ($criteriaDto->orderBy()) {

            $this->addCriteriaOrderBy($criteriaDto->orderBy());
        }
        return $this;
    }

    private function addCriteriaOrderBy(array $orderBy): QueryBuilder
    {
        return $this->queryBuilder->addOrderBy(sort: $orderBy['sort'], order: $orderBy['operator']);
    }

    private function addCriteriaFilter(array $filterBy): QueryBuilder
    {
        foreach ($filterBy as $key => $value) {
            $this->queryBuilder
                // ->andWhere($key . ' = :criteria_' . $key)
                // ->setParameter('criteria_' . $key, $value);
                ->andWhere(sprintf('lower(unaccent(%s::TEXT)) = lower(unaccent(:criteria_%s))', $key, $key))
                ->setParameter(sprintf('criteria_%s', $key), $value);
        }

        return $this->queryBuilder;
    }

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
                        "unaccent(lower(data -> %s)) = unaccent(lower('%s'))",
                        $key,
                        $value,
                    )
                );
        }

        return $this->queryBuilder;
    }
}
