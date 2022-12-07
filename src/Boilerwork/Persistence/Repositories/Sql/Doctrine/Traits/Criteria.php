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

    private function addCriteriaOrderBy(string $orderBy): QueryBuilder
    {
        $orderParsed = explode(',', $orderBy);
        return $this->queryBuilder->addOrderBy(sort: $orderParsed[0], order: $orderParsed[1]);
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


    /*
    public function convertWhereOperators($valor): string
    {
        $conver = array(
            "EQ" => "=",
            "GT" => ">",
            "LT" => "<",
            "GE" => ">=",
            "LE" => "<=",
            "LIKE" => "LIKE",
            "NE" => "!="
        );

        if (!$conver[$valor]) {
            throw new Exception('Operator value "' . $valor . '" not permitted ', 400);
        }

        $valor = strtoupper($valor);

        return $conver[$valor];
    }

    public function generateConditions($query): object
    {
        if ($this->where) {
            $query = $this->generateWhere($query);
        }

        if ($this->orderBy) {
            $query = $this->generateOrderBy($query);
        }

        return $query;
    }

    private function generateWhere($query)
    {
        $conditionsWhere = explode($this->mainSeparator, $this->where);

        for ($x = 0; $x < count($conditionsWhere); $x++) {
            $where_parts = explode($this->internalSeparator, $conditionsWhere[$x]);
            $operator = $this->convertWhereOperators($where_parts[1]);
            // @TODO hay que bindear el campo field, el problema es que por detras, la libreria al bindear el valor agregara unas comillas que hace que no encuentre el campo
            if ($operator === "LIKE") {
                $query->where($where_parts[0] . " " . $operator . " :valueWhere" . $x);
                $query->bindValues([
                    ":valueWhere" . $x => "%" . $where_parts[2] . "%"
                ]);
            } else {
                $query->where($where_parts[0] . $operator . ":valueWhere" . $x);
                $query->bindValues([
                    ":valueWhere" . $x => $where_parts[2]
                ]);
            }
        }

        return $query;
    }

    private function convertSort($valor): string
    {
        $conver = array("ASC", "DESC");

        if (!in_array($valor, $conver)) {
            throw new Exception('Sort value "' . $valor . '" not permitted ', 400);
        }

        return $valor;
    }

    private function generateOrderBy($query)
    {
        $conditionsOrderBy = explode($this->mainSeparator, $this->orderBy);

        for ($x = 0; $x < count($conditionsOrderBy); $x++) {
            // @TODO mismo problema que arriba, si bindeamos el dato le agrega comillas
            $orderBy_parts = explode($this->internalSeparator, $conditionsOrderBy[$x]);
            $query->orderBy([$orderBy_parts[0] . ' ' . $this->convertSort($orderBy_parts[1])]);
        }

        return $query;
    }
    */
}
