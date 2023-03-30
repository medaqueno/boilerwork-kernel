#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits;

use Boilerwork\Persistence\QueryBuilder\CriteriaExtendedDto;
use Doctrine\DBAL\Query\QueryBuilder;

trait CriteriaExtended
{

    public function addCriteriaExtended(CriteriaExtendedDto $criteriaDto): self
    {
        $filterBy = array_filter($criteriaDto->params()); // Remove null values
        //var_dump('filterBy', $filterBy);
        if ($filterBy) {
            $this->addCriteriaExtendedFilter($filterBy);
        }
        //var_dump('criteriaDto->orderBy', $criteriaDto->orderBy());
        if ($criteriaDto->orderBy()) {
            $this->addCriteriaExtendedOrderBy($criteriaDto->orderBy());
        }
        return $this;
    }
    private function addCriteriaExtendedFilter(array $filterBy): QueryBuilder
    {
        foreach ($filterBy as $key => $value) {
            $keyArray = explode('|', $key);
            //is jsonB field
            (isset($keyArray[2])) ? $this->buildQueryJsonb($keyArray, $value) : $this->buildQuery($keyArray, $value);
        }
        //var_dump($this->queryBuilder->getSQL());
        return $this->queryBuilder;
    }
    private function addCriteriaExtendedOrderBy(array $orderBy): QueryBuilder
    {
        $query = $this->queryBuilder->addOrderBy(sort: $orderBy['sort'], order: $orderBy['operator']);

        return $query;
    }
    private function buildQueryJsonb(array $keyArray, string $filterValue): QueryBuilder
    {
        $arrayValues = explode(',', $filterValue);
        return (count($arrayValues) > 1) ? 
            $this->buildMultiValueJsonbQuery($keyArray, $arrayValues) :
            $this->queryBuilder->andWhere($this->buildJsonbWhere($keyArray[2], $keyArray[0], $filterValue));
    }

    private function buildMultiValueJsonbQuery(array $keyArray, array $arrayValues): QueryBuilder
    {
        $statements = $this->queryBuilder->expr()->orX();
        foreach ($arrayValues as $val) {
            $statements->add(
                $this->buildJsonbWhere($keyArray[2], $keyArray[0], $val)
            );
        }
        return $this->queryBuilder->andWhere($statements);
    }

    private function buildMultiValueQuery(array $keyArray, array $arrayValues): QueryBuilder
    {
        $statements = $this->queryBuilder->expr()->orX();
        foreach ($arrayValues as $val) {
            $statements->add(sprintf("(%s)::text LIKE '%%%s%%'", $keyArray[0], $val));
        }
        return $this->queryBuilder->andWhere($statements);
    }

    private function buildQuery(array $keyArray, string $filterValue): QueryBuilder
    {
        $arrayValues = explode(',', $filterValue);
        return (count($arrayValues) > 1) ?
            $this->buildMultiValueQuery($keyArray, $arrayValues) :
            $this->queryBuilder
                ->andWhere($keyArray[0] . ' = :where_'.$keyArray[0])
                ->setParameter("where_".$keyArray[0], $filterValue)
        ;
    }
    private function buildJsonbWhere(string $jsonBKey, string $param, string $value): string
    {
        return sprintf(
            "jsonb_path_exists(%s, '$.**.%s ? (@ like_regex \"%s\")')",
            $jsonBKey,
            $param,
            $value,
        );
    }


}