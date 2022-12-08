#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\QueryBuilder;

use Boilerwork\Validation\Assert;

final class CriteriaDto
{
    private function __construct(
        private readonly array $params,
        private readonly ?string $orderBy,
    ) {
        Assert::lazy()
            ->that($orderBy)
            ->nullOr()
            // Only allow <string>,<ASC DESC asc desc> format
            ->regex('/\A([A-Za-z])+[,]+((ASC|DESC|asc|desc))\z/', 'OrderBy clause is not valid', 'criteriaOrderBy.invalidValue')
            ->verifyNow();

        if ($orderBy) {
            // Only allow order by fields existing in params
            Assert::lazy()->that($params)
                ->keyExists(explode(',', $orderBy)[0], sprintf('Sort field must be a valid value: %s', implode(',', array_keys($params))), 'criteriaSortValue.notAllowed')
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

        return [
            'sort' => $orderBy[0],
            'operator' => $orderBy[1],
        ];
    }

    public static function create(array $params = [], string $orderBy = null): static
    {
        return new static(
            params: $params,
            orderBy: $orderBy
        );
    }

    public function hash(): string
    {
        return md5(implode('', $this->params) . $this->orderBy ?? '');
    }
}
