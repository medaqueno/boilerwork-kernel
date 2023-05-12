#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Http;

use Boilerwork\Support\ValueObjects\Language\Language;
use Boilerwork\Validation\Assert;
use Psr\Http\Message\ServerRequestInterface;

/**
 * QueryCriteria builder class.
 *
 * Builds query criteria objects from a HTTP request. Allows mapping external parameters
 * to internal names, storing filtering, sorting, pagination and hashing params.
 */
final class QueryCriteria
{
    private array $params = [];
    private array $originalParams = [];
    private ?string $orderBy;
    private string $language = Language::FALLBACK;
    private ServerRequestInterface $request;

    public static function createFromRequest(ServerRequestInterface $request): self
    {
        $instance          = new self();
        $instance->request = $request;

        return $instance;
    }

    /**
     * Add a search parameter.
     *
     * Searches for the given external parameter in the request and stores it under
     * the internal name.
     *
     * @param string $external External parameter name.
     * @param string $internal Internal parameter name.
     * @return self
     */
    public function addSearch(string $external, string $internal): self
    {
        $rawQueryParams = $this->request->getQueryParams();

        if (isset($rawQueryParams[$external])) {
            $this->params[$internal] = $rawQueryParams[$external];
        }

        return $this;
    }

    /**
     * Add a filter parameter.
     *
     * Searches for filter[$external] in the request. Can also accept an optional $displayValue.
     *
     * @param string $external   External parameter name.
     * @param string $internal   Internal parameter name.
     * @param string $displayValue Optional displayed value for the filter.
     * @return self
     */
    public function addFilter(string $external, string $internal, $displayValue = null): self
    {
        $rawQueryParams = $this->request->getQueryParams();

        $this->originalParams[$internal] = [
            'external'      => $external,
            'value'         => isset($rawQueryParams['filter'][$external]) ? $rawQueryParams['filter'][$external] : null,
            'displayValue' => $displayValue,
        ];

        if (isset($rawQueryParams['filter'][$external])) {
            $this->params['filter'][$internal] = [
                'external' => $external,
                'value'    => $rawQueryParams['filter'][$external],
            ];
        }

        return $this;
    }

    /**
     * Sets the language.
     *
     * @param string $language Language code. Defaults to platform fallback
     * @return self
     */
    public function addLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Build the query criteria.
     *
     * Performs any additional logic like adding sorting and pagination params from the request.
     *
     * @return self
     */
    public function build(): self
    {
        $rawQueryParams = $this->request->getQueryParams();

        if (isset($rawQueryParams['order_by'])) {
            $this->params['order_by'] = $rawQueryParams['order_by'];
        }

        if (isset($rawQueryParams['page']) && isset($rawQueryParams['per_page'])) {
            $this->params['page']     = (int)$rawQueryParams['page'];
            $this->params['per_page'] = (int)$rawQueryParams['per_page'];
        }

        $this->convertStringToBoolean($this->params);
        $this->orderBy = isset($this->params['order_by']) ? $this->params['order_by'] : null;

        $this->validate();

        return $this;
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
        $sortingParam = $this->getSortingParam();

        if ($sortingParam) {
            $sort = $this->getSortingParam()['sort'];
            Assert::lazy()
                ->that($sort)
                ->satisfy(function () use ($sort) {
                    $searchParam = $sort;
                    $result      = array_filter($this->originalParams, function ($item) use ($searchParam) {
                        return $item['external'] === $searchParam;
                    });

                    return array_keys($result)[0] ?? false;
                },
                    sprintf('Sorting is not allowed for: %s', $sort),
                    'sortingParam.invalidSortValue')
                ->that($sortingParam['sort'])
                ->regex(
                    '/\A[A-Za-z0-9_.-]+\z/',
                    'Sort field accepts alphabetical, numeric, . - _ characters',
                    'sortingParam.invalidSortValue',
                )
                ->that($sortingParam['operator'])
                ->regex(
                    '/\A(ASC|DESC|asc|desc)\z/',
                    'Operator accepts only ASC, DESC, asc or desc',
                    'sortingParam.invalidOperatorValue',
                )
                ->verifyNow();
        }
    }

    public function getAllParams(): array
    {
        return $this->params;
    }

    public function getSearchParams(): array
    {
        return array_diff_key($this->params, array_flip(['page', 'per_page', 'order_by', 'filter']));
    }

    public function getPagingParams(): ?array
    {
        $keys = ['page', 'per_page'];

        return array_intersect_key($this->params, array_flip($keys)) ?: null;
    }

    public function getSortingParam(): ?array
    {
        if ($this->orderBy === null) {
            return null;
        }

        $orderBy = explode(',', $this->orderBy);

        return [
            'sort'     => $orderBy[0],
            'operator' => $orderBy[1],
        ];
    }

    /**
     * Returns an array with filter mapped params included in the query string
     */
    public function getFilterParams(): array
    {
        return $this->params['filter'] ?? [];
    }

    /**
     * Returns an array with all filter mapped params, included or not in the query string
     */
    public function getAllFilterParams(): array
    {
        return $this->originalParams;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Generates a hash for the query criteria sorting an associative array and any nested arrays it contains to
     * ensure same hash is always returned with the same query string no matter the order of params.
     *
     * @return string hash of the query criteria.
     */
    public function hash(): string
    {
        $params = $this->params;
        $this->recursiveKsort($params);

        if ($this->orderBy !== null) {
            $orderByParts = explode(',', $this->orderBy);
            sort($orderByParts);
            $orderBy = implode(',', $orderByParts);
        } else {
            $orderBy = '';
        }

        $serializedParams = serialize($params);

        return md5($serializedParams . $orderBy . ($this->language ?? ''));
    }

    /**
     * This method sorts the keys of the given associative array in ascending order. If the array contains
     * any nested arrays, those will be sorted as well.
     *
     * If a nested array is indexed (i.e., its keys are consecutive integers), its values will be sorted in
     * ascending order. This ensures that arrays with the same values but in different orders are treated as
     * equivalent.
     */
    private function recursiveKsort(&$array): true
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                // Check if array is indexed
                if (array_keys($value) === range(0, count($value) - 1)) {
                    sort($value);
                } else {
                    $this->recursiveKsort($value);
                }
            }
        }
        return ksort($array);
    }
}
