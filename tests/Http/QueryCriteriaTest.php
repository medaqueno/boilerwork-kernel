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

        $queryCriteria = QueryCriteria::createFromRequest($request, [
            'destination' => 'destination',
            'price' => 'price_internal'
        ], 'en');

        $this->assertEquals([
            'destination' => 'test',
            'filter' => [
                'price_internal' => '100-200'
            ],
            'order_by' => 'destination,asc',
            'page' => 2,
            'per_page' => 25
        ], $queryCriteria->getAllParams());

        $this->assertEquals([
            'destination' => 'test'
        ], $queryCriteria->getSearchParams());

        $this->assertEquals([
            'page' => 2,
            'per_page' => 25
        ], $queryCriteria->getPagingParams());

        $this->assertEquals([
            'sort' => 'destination',
            'operator' => 'asc'
        ], $queryCriteria->getSortingParam());

        $this->assertEquals([
            'price_internal' => '100-200'
        ], $queryCriteria->getFilterParams());

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

        $queryCriteria = QueryCriteria::createFromRequest($request, [
            'destination' => 'destination',
            'price' => 'price_internal'
        ], 'en');

        $this->assertEquals([
            'destination' => 'test',
            'filter' => [
                'price_internal' => '100-200',
            ],
            'order_by' => 'destination,asc',
        ], $queryCriteria->getAllParams());
        $this->assertEquals('en', $queryCriteria->getLanguage());
        $this->assertNull($queryCriteria->getPagingParams());
    }
/*
    public function testWithOrderByDescending(): void
    {
        $url = '/example?destination=test&order_by=name,desc&page=2&per_page=25';
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

        $queryCriteria = QueryCriteria::createFromRequest($request, [
            'destination' => 'destination',
            'price' => 'price_internal'
        ], 'en');

        $this->assertEquals([
            'destination' => 'test',
            'order_by' => 'name,desc',
            'page' => 2,
            'per_page' => 25,
        ], $queryCriteria->getAllParams());
        $this->assertEquals('en', $queryCriteria->getLanguage());
        $this->assertEquals([
            'sort' => 'name',
            'operator' => 'desc'
        ], $queryCriteria->getSortingParam());
    }

    public function testWithInvalidOrderBy(): void
    {
        $url = '/example?destination=test&language=en&order_by=name,invalid&page=2&per_page=25';
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

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('OrderBy clause accepts alphabetical, numeric and - _ characters and must include sort and operator');

        QueryCriteria::createFromRequest($request, [
            'destination' => 'destination',
            'price' => 'price_internal'
        ]);
    }*/

///////




    public function testInvalidOrderByField()
    {
        $this->expectException(\Boilerwork\Validation\CustomAssertionFailedException::class);
        $this->expectExceptionMessage("Order by is only allowed for the fields");

        $url = '/example?destination=test&order_by=invalid_field,asc,desc&page=2&per_page=25';
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

        $paramMapping = [
            'search' => 'search_internal',
            'price' => 'price_internal',
        ];

        QueryCriteria::createFromRequest($request, $paramMapping);
    }

    public function testValidOrderBy()
    {
        $url = '/example?destination=test&order_by=price,asc';
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

        $paramMapping = [
            'search' => 'search_internal',
            'price' => 'price_internal',
        ];

        $queryCriteria = QueryCriteria::createFromRequest($request, $paramMapping);

        $this->assertEquals(
            [
                'sort' => 'price_internal',
                'operator' => 'asc',
            ],
            $queryCriteria->getSortingParam()
        );
    }

        public function testInvalidOrderByOperator()
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


            $paramMapping = [
                'search' => 'search_internal',
                'price' => 'price_internal',
            ];

            QueryCriteria::createFromRequest($request, $paramMapping);
        }

        public function testOrderByWithEmptyOperatorDefaultsToNull()
        {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage("Operator accepts only ASC, DESC, asc or desc");

            $url = '/example?destination=test&order_by=price';
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


            $paramMapping = [
                'search' => 'search_internal',
                'price' => 'price_internal',
            ];

            $queryCriteria = QueryCriteria::createFromRequest($request, $paramMapping);

            $this->assertEquals(
                [
                    'sort' => 'price_internal',
                    'operator' => null,
                ],
                $queryCriteria->getSortingParam()
            );
        }
}
