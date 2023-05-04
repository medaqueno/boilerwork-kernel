#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Http;

use Boilerwork\Validation\Assert;
use Psr\Http\Message\ServerRequestInterface;

final class QueryCriteria
{
    private array $params;
    private ?string $orderBy;
    private ?string $language;

    public static function createFromRequest(ServerRequestInterface $request, array $params, $language = null): self
    {
        $queryParams = [];
        $rawQueryParams = $request->getQueryParams();

        foreach ($params as $externalParam => $internalParam) {
            $isFilter = isset($rawQueryParams['filter'][$externalParam]);
            $value = $isFilter ? $rawQueryParams['filter'][$externalParam] : $rawQueryParams[$externalParam] ?? null;
            if ($value !== null) {
                if ($isFilter) {
                    $queryParams['filter'][$internalParam] = $value;
                } else {
                    $queryParams[$internalParam] = $value;
                }
            }
        }

        if (isset($rawQueryParams['order_by'])) {
            $queryParams['order_by'] = $rawQueryParams['order_by'];
        }

        if (isset($rawQueryParams['page']) && isset($rawQueryParams['per_page'])) {
            $queryParams['page'] = (int)$rawQueryParams['page'];
            $queryParams['per_page'] = (int)$rawQueryParams['per_page'];
        }

        return new self($queryParams, $language);
    }

    private function __construct(array $queryParams, $language = null)
    {
        $this->params = $queryParams;
        $this->convertStringToBoolean($this->params);
        $this->orderBy = isset($this->params['order_by']) ? $this->params['order_by'] : null;
        $this->language = $language;

        $this->validate();
    }

    private function convertStringToBoolean(&$params)
    {
        array_walk_recursive($params, function (&$value) {
            if ($value === "true") {
                $value = true;
            } elseif ($value === "false") {
                $value = false;
            }
        });
    }

    private function validate(): void
    {
        Assert::lazy()
            ->that($this->orderBy)
            ->nullOr()
            // Only allow <string>,<ASC DESC asc desc> format
            ->regex('/\A([A-Za-z0-9_-])+[,]+((ASC|DESC|asc|desc))\z/', 'OrderBy clause accepts alphabetical, numeric and - _ characters and must include sort and operator', 'criteriaOrderBy.invalidValue')
            ->verifyNow();
    }

    public function getAllParams(): array
    {
        return $this->params;
    }

    public function getSearchParams(): array
    {
        return array_diff_key($this->params, array_flip(['page', 'per_page', 'order_by', 'filter']));
    }

    /**
     * @return array{page: int, per_page: int}|null
     */
    public function getPagingParams(): ?array
    {
        $keys = ['page', 'per_page'];
        return array_intersect_key($this->params, array_flip($keys)) ?: null;
    }

    /**
     * @return array{field: string, order: string}|null An associative array with 'field' and 'order' keys if sorting is set, or null if not set.
     */
    public function getSortingParam(): ?array
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

    public function getFilterParams(): array
    {
        return isset($this->params['filter']) ? $this->params['filter'] : [];
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function hash(): string
    {
        return md5(json_encode($this->params) . ($this->orderBy ?? '') . ($this->language ?? ''));
    }
}
