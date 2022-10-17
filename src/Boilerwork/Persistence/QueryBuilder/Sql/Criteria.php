#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\QueryBuilder\Sql;
use Exception;

final class Criteria
{

    private string $mainSeparator = "|";
    private string $internalSeparator = ",";

    public function __construct(
        private readonly string $where = "",
        private readonly string $orderBy = "",
        private array $conditionsWhere = [],
        private array $conditionsOrderBy = []
    ) {
        $this->loadConditions();
        container()->instance('Criteria', $this);
    }

    public function convertOperators($valor): string
    {
        $conver = array(
            "E" => "=",
            "GT" => ">",
            "LT" => "<",
            "GTE" => ">=",
            "LTE" => "<=",
            "LIKE" => "LIKE",
            "NE" => "!="/*,
            "BTW" => "BETWEEN",
            "IN" => "IN"*/
        );

        if(!$conver[$valor]){
            throw new Exception('Operator value "'.$valor.'" not permitted ',400);
        }

        $valor = strtoupper($valor);

        return $conver[$valor];
    }

    public function convertSort($valor): string
    {
        $conver = array("ASC","DESC");

        if(!in_array($valor,$conver)){
            throw new Exception('Sort value "'.$valor.'" not permitted ',400);
        }

        return $valor;
    }

    public function generateConditions($query): object
    {
        for ($x = 0; $x < count($this->conditionsWhere); $x++) {
            $where_parts = explode($this->internalSeparator, $this->conditionsWhere[$x]);
            $operator = $this->convertOperators($where_parts[1]);
            //todo hay que bindear el campo field, el problema es que por detras, la libreria al bindear el valor agregara unas comillas que hace que no encuentre el campo
            if ($operator === "LIKE") {
                $query->where($where_parts[0] . " " . $operator . " :valueWhere" . $x);
                $query->bindValues([
                    //":fieldWhere" . $x => $where_parts[0],
                    ":valueWhere" . $x => "%" . $where_parts[2] . "%"
                ]);
            } else {
                $query->where($where_parts[0] . $operator . ":valueWhere" . $x);
                $query->bindValues([
                    //":fieldWhere" . $x => $where_parts[0],
                    ":valueWhere" . $x => $where_parts[2]
                ]);
            }
        }
        
        //Lo separaremos en dos funciones, una para el where y otra para el orderby

        for ($x = 0; $x < count($this->conditionsOrderBy); $x++) {
            //todo mismo problema que arriba, si bindeamos el dato le agrega comillas
            $orderBy_parts = explode($this->internalSeparator, $this->conditionsOrderBy[$x]);
            $query->orderBy([$orderBy_parts[0] . ' ' . $this->convertSort($orderBy_parts[1])]);
           
            //$query->bindValues([":fieldOrder" . $x => $orderBy_parts[0]]);
            //$query->bindValues([":valueOrder" . $x => $orderBy_parts[1]]);
        }

        return $query;
    }

    private function loadConditions()
    {
        if ($this->where != "") {
            $this->conditionsWhere = explode($this->mainSeparator, $this->where);
        }

        if ($this->orderBy != "") {
            $this->conditionsOrderBy = explode($this->mainSeparator, $this->orderBy);
        }
    }

    public function serialize(): array
    {
        return [
            //'condition' => $this->generateCondition(),
        ];
    }
}
