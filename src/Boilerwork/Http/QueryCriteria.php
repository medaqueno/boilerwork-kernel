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
 *
 */
final class QueryCriteria
{
    private array $params = [];
    private array $originalFilterParams = [];
    private array $sortableFields = [];
    private ?string $orderBy;
    private string $language = Language::FALLBACK;
    private ServerRequestInterface $request;

    const CAST_STRING = 'string';
    const CAST_INT = 'int';
    const CAST_FLOAT = 'float';
    const CAST_BOOL = 'bool';
    const CAST_NULL = 'null';

    /**
     * Create QueryCriteria from a request.
     *
     * Parses and normalizes query string parameters to build query criteria.
     *
     * Accepted parameters include:
     *
     * - <search field name> - Search values
     * - filter[<field name>] - Filter values
     * - filter[<field name>][] - Multi-value filters
     * - order_by - Sorting (field,direction)
     * - page - Pagination page
     * - per_page - Pagination per page
     *
     * - lang - must be added manually
     *
     * For example:
     *
     * search?page=1&per_page=25&order_by=iso2,asc&name=Vibra Monterrey Aparthotel&
     *      filter[categories]=1-3&filter[active][]=true&filter[active][]=false&
     *      id=0187b3e1-f74a-78a7-9f30-397e2055dadb&filter[iso2]=FR
     *
     */
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
     * @param  string  $external  External parameter name.
     * @param  string  $internal  Internal parameter name.
     * @param  string  $castTo  Optional value type casting. Defaults to string
     *
     * @return self
     */
    public function addSearch(string $external, string $internal, string $castTo = QueryCriteria::CAST_STRING): self
    {
        $rawQueryParams = $this->request->getQueryParams();

        if (isset($rawQueryParams[$external])) {
            $this->params['search'][$internal] = [
                'external' => $external,
                'value'    => is_array($rawQueryParams[$external]) ? $rawQueryParams[$external] : $this->castString(
                    $rawQueryParams[$external],
                    $castTo,
                ),
            ];
        }

        $this->sortableFields[$external] = $internal;

        return $this;
    }

    /**
     * Add a filter parameter.
     *
     * Searches for filter[$external] in the request. Can also accept an optional $displayValue.
     *
     * @param  string  $external  External parameter name.
     * @param  string  $internal  Internal parameter name.
     * @param  string  $displayValue  Optional displayed value for the filter.
     * @param  string  $castTo  Optional value type casting. Defaults to string
     *
     * @return self
     */
    public function addFilter(
        string $external,
        string $internal,
        string $displayValue = null,
        string $castTo = QueryCriteria::CAST_STRING,
    ): self {
        $rawQueryParams = $this->request->getQueryParams();

        $filterValue = isset($rawQueryParams['filter'][$external])
            ? (is_array($rawQueryParams['filter'][$external])
                ? $rawQueryParams['filter'][$external]
                : $this->castString($rawQueryParams['filter'][$external], $castTo)
            )
            : null;

        $this->originalFilterParams[$internal] = [
            'external'     => $external,
            'value'        => $filterValue,
            'displayValue' => $displayValue,
        ];

        if (isset($rawQueryParams['filter'][$external])) {
            $this->params['filter'][$internal] = [
                'external' => $external,
                'value'    => $filterValue,
            ];
        }

        $this->sortableFields[$external] = $internal;

        return $this;
    }

    /**
     * Sets the language.
     *
     * @param  string  $language  Language code. Defaults to platform fallback
     *
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
            $orderParams    = explode(',', $rawQueryParams['order_by']);
            $orderField     = $orderParams[0];
            $orderDirection = $orderParams[1];

            foreach ($this->sortableFields as $internal => $external) {
                if ($external === $orderField) {
                    $orderField = $internal;
                    break;
                }
            }

            $this->params['order_by'] = $orderField . ',' . $orderDirection;
        }

        $this->orderBy = isset($this->params['order_by']) ? $this->params['order_by'] : null;

        if (isset($rawQueryParams['page']) && isset($rawQueryParams['per_page'])) {
            $this->params['page']     = (int)$rawQueryParams['page'];
            $this->params['per_page'] = (int)$rawQueryParams['per_page'];
        }

        $this->validate();

        return $this;
    }

    private function castString(string $value, string $castTo): mixed
    {
        return match ($castTo) {
            QueryCriteria::CAST_NULL => null,
            QueryCriteria::CAST_INT => (int)$value,
            QueryCriteria::CAST_FLOAT => (float)$value,
            QueryCriteria::CAST_BOOL => (bool)$value,
            default => (string)$value,
        };
    }

    private function validate(): void
    {
        $sortingParam = $this->getSortingParam();

        if ($sortingParam) {
            $sort = $sortingParam['sort'];
            Assert::lazy()
                ->that($sortingParam['sort'])
                ->satisfy(function ($item) use ($sortingParam) {

                    $sortExternal = explode(',', $this->orderBy)[0];

                    return array_reduce(
                        array_merge($this->getSearchParams(), $this->getAllFilterParams()),
                        function ($carry, $item) use ($sortExternal) {
                            return $carry || $item['external'] == $sortExternal;
                        },
                        false
                    );

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
        return $this->params['search'] ?? [];
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

        $orderBy           = explode(',', $this->orderBy);

        $sortFieldInternal = $this->sortableFields[$orderBy[0]] ?? $orderBy[0];

        return [
            'sort'     => $sortFieldInternal,
            'operator' => $orderBy[1] ?? null,
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
        return $this->originalFilterParams;
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
