#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\QueryBuilder;

use Boilerwork\Validation\Assert;

final class CriteriaExtendedDto
{
    private function __construct(
        private readonly array $params,
        private readonly ?string $orderBy,

    )
    {
        Assert::lazy()
            ->that($orderBy)
            ->nullOr()
            // Only allow <string>,<ASC DESC asc desc> format
            ->regex('/\A([A-Za-z0-9_-])+[,]+((ASC|DESC|asc|desc))\z/', 'OrderBy clause accepts alphabetical, numeric and - _ characters. Only allow <string>,<ASC DESC asc desc> format', 'criteriaOrderBy.invalidValue')
            ->verifyNow();

        if ($orderBy) {
            // Only allow order by fields existing in params
            $paramsAlias = array_map(fn($item): string => explode('|', $item)[1], array_keys($params));
            Assert::lazy()
                ->that(explode(',', $orderBy)[0])
                ->inArray($paramsAlias, sprintf('Sort field must be a valid value: %s', implode(',', $paramsAlias)), 'criteriaSortValue.notAllowed')
                ->verifyNow();
        }
    }

    public function params(): array
    {
        return $this->params;
    }

    /**
     * @return ?array{sort: string, operator: string}
     */
    public function orderBy(): ?array
    {
        if ($this->orderBy === null) {
            return null;
        }

        $orderBy = explode(',', $this->orderBy);
        $keyParamsByOrder = array_key_first(array_filter(array_keys($this->params), fn($item) => explode('|', $item)[1] === $orderBy[0]));
        $filtersArray = explode('|', array_keys($this->params)[$keyParamsByOrder]);
        if (isset($filtersArray[2])) {
            $sortArray = explode('.', $filtersArray[0]);
            $sortWrap = array_map(fn($item): string => "'" . $item . "'", $sortArray);
            if (isset($filtersArray[3])) {
                $sort = $filtersArray[2] . "->" . implode('->', $sortWrap) . "->>'" . $filtersArray[3] . "'";
            } else {
                $sortLast = array_pop($sortWrap);
                $sort = $filtersArray[2] . "->" . implode('->', $sortWrap) . "->>" . $sortLast;
            }
        } else {
            $sort = $filtersArray[0];
        }

        return [
            'sort' => $sort,
            'operator' => $orderBy[1],
        ];
    }

    public static function create(array $params = [], ?string $orderBy = null): static
    {
        return new static (
            params: $params,
            orderBy: $orderBy
        );
    }


    public function hash(): string
    {
        return md5(implode('', $this->params) . $this->orderBy ?? '');
    }
}