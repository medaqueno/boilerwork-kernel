#!/usr/bin/env php
<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\ServerRequest;
use Boilerwork\Http\QueryCriteria;

final class QueryCriteriaTest extends TestCase
{
    public function testWithAllParameters(): void
    {
        $url = '/example?destination=test&order_by=destination,asc&filter[price]=100-200&page=2&per_page=25';
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $queryParams);

        $request = new ServerRequest(
            [],
            [],
            $url,
            'GET',
            'php://input',
            [],
            [],
            $queryParams
        );

        $queryCriteria = QueryCriteria::createFromRequest($request)
            ->addSearch('destination', 'destination_internal')
            ->addFilter('price', 'price_internal')
            ->addFilter('category', 'category_internal')
            ->addLanguage('en')
            ->build();

        $this->assertEquals([
            'search' => [
                'destination_internal' => ['external' => 'destination', 'value' => 'test']
            ],
            'filter' => [
                'price_internal' => ['external' => 'price', 'value' => '100-200']
            ],
            'order_by' => 'destination,asc',
            'page' => 2,
            'per_page' => 25
        ], $queryCriteria->getAllParams());

        $this->assertEquals([
            'destination_internal' => ['external' => 'destination', 'value' => 'test']
        ], $queryCriteria->getSearchParams());

        $this->assertEquals([
            'page' => 2,
            'per_page' => 25
        ], $queryCriteria->getPagingParams());

        $this->assertEquals([
            'sort' => 'destination_internal',
            'operator' => 'asc'
        ], $queryCriteria->getSortingParam());

        $this->assertEquals([
            'price_internal' => [
                'external' => 'price',
                'value' => '100-200'
            ]
        ], $queryCriteria->getFilterParams());

        $this->assertEquals([
            'price_internal' => [
                'external' => 'price',
                'value' => '100-200',
                'displayValue' => null
            ], 'category_internal' => [
                'external' => 'category',
                'value' => null,
                'displayValue' => null
            ]
        ], $queryCriteria->getAllFilterParams());

        $this->assertEquals('en', $queryCriteria->getLanguage());
    }

    public function testWithoutPagingParams(): void
    {
        $url = '/example?destination=test&order_by=destination,asc&filter[price]=100-200';
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $queryParams);

        $request = new ServerRequest(
            [],
            [],
            $url,
            'GET',
            'php://input',
            [],
            [],
            $queryParams
        );


        $queryCriteria = QueryCriteria::createFromRequest($request)
            ->addSearch('destination', 'destination')
            ->addFilter('price', 'price_internal')
            ->addLanguage('en')
            ->build();

        $this->assertEquals([
            'search' => [
                'destination' => [
                    'external' => 'destination',
                    'value' => 'test',
                ],
            ],
            'filter' => [
                'price_internal' => [
                    'external' => 'price',
                    'value' => '100-200',
                ]
            ],
            'order_by' => 'destination,asc',
        ], $queryCriteria->getAllParams());
        $this->assertEquals('en', $queryCriteria->getLanguage());
        $this->assertNull($queryCriteria->getPagingParams());
    }

    public function testInvalidOrderByOperator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Operator accepts only ASC, DESC, asc or desc");

        $url = '/example?destination=test&order_by=price,INVALID';
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $queryParams);

        $request = new ServerRequest(
            [],
            [],
            $url,
            'GET',
            'php://input',
            [],
            [],
            $queryParams
        );

        QueryCriteria::createFromRequest($request)
            ->addSearch('destination', 'destination')
            ->addFilter('price', 'price_internal')
            ->addLanguage('en')
            ->build();
    }
}
