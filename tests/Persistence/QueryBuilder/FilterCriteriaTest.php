#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Persistence\QueryBuilder\FilterCriteria;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

// use Deminy\Counit\TestCase;

final class FilterCriteriaTest extends TestCase
{
    private FilterCriteria $filterCriteria;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filterCriteria = new FilterCriteria();
    }

    public function testPostFilterWithNestedArrays(): void
    {
        $results = [
            [
                'id' => 1,
                'name' => 'John',
                'tags' => [
                    'programming',
                    'sports'
                ],
                'details' => [
                    'age' => 25,
                    'address' => [
                        'city' => 'New York',
                        'country' => 'USA',
                    ],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Jane',
                'tags' => [
                    'design',
                    'art'
                ],
                'details' => [
                    'age' => 30,
                    'address' => [
                        'city' => 'Los Angeles',
                        'country' => 'USA',
                    ],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Jack',
                'tags' => [
                    'sports',
                    'travel'
                ],
                'details' => [
                    'age' => 35,
                    'address' => [
                        'city' => 'London',
                        'country' => 'UK',
                    ],
                ],
            ],
        ];

        // Filtrar por tags
        $postFilter = [
            'tags' => ['external' => 'tags', 'value' => 'sports']
        ];

        $filteredResults = $this->filterCriteria->setData($results)
            ->postFilter($postFilter)
            ->getResults();

        // Debería devolver dos resultados: John y Jack tienen 'sports' en sus tags
        $this->assertCount(2, $filteredResults);
        $this->assertEquals(1, $filteredResults[0]['id']);
        $this->assertEquals(3, $filteredResults[1]['id']);

        // Filtrar por detalles anidados (por ejemplo, country)
        $postFilter = [
            'details.address.country' => ['external' => 'details.address.country', 'value' => 'USA']
        ];

        $filteredResults = $this->filterCriteria->setData($results)
            ->postFilter($postFilter)
            ->getResults();

        // Debería devolver dos resultados: John y Jane tienen 'USA' como país
        $this->assertCount(2, $filteredResults);
        $this->assertEquals(1, $filteredResults[0]['id']);
        $this->assertEquals(2, $filteredResults[1]['id']);
    }

    public function testEqualOperator(): void
    {
        $data = [
            ['value' => 5],
            ['value' => 10],
            ['value' => 15],
        ];

        $filteredData = $this->filterCriteria->setData($data)
            ->postFilter(['value' => ['external' => 'value', 'value' => 10]])
            ->getResults();

        $this->assertCount(1, $filteredData);
        $this->assertEquals(10, $filteredData[0]['value']);
    }

    public function testGreaterThanOperator(): void
    {
        $data = [
            ['value' => 5],
            ['value' => 10],
            ['value' => 15],
        ];

        $filteredData = $this->filterCriteria->setData($data)
            ->postFilter(['value' => ['external' => 'value', 'value' => '≥10']])
            ->getResults();

        $this->assertCount(2, $filteredData);
        $this->assertContains(['value' => 10], $filteredData);
        $this->assertContains(['value' => 15], $filteredData);
    }

    public function testLessThanOperator(): void
    {
        $data = [
            ['value' => 5],
            ['value' => 10],
            ['value' => 15],
        ];

        $filteredData = $this->filterCriteria->setData($data)
            ->postFilter(['value' => ['external' => 'value', 'value' => '≤10']])
            ->getResults();

        $this->assertCount(2, $filteredData);
        $this->assertContains(['value' => 5], $filteredData);
        $this->assertContains(['value' => 10], $filteredData);
    }

    public function testMultipleValues(): void
    {
        $data = [
            ['value' => 5],
            ['value' => 10],
            ['value' => 15],
        ];

        $filteredData = $this->filterCriteria->setData($data)
            ->postFilter(['value' => ['external' => 'value', 'value' => [5, 15]]])
            ->getResults();

        $this->assertCount(2, $filteredData);
        $this->assertContains(['value' => 5], $filteredData);
        $this->assertContains(['value' => 15], $filteredData);
    }

    public function testOrderByDesc()
    {
        $filterCriteria = new FilterCriteria();

        $data = [
            ['id' => 3, 'name' => 'John', 'age' => 30],
            ['id' => 1, 'name' => 'Alice', 'age' => 25],
            ['id' => 2, 'name' => 'Bob', 'age' => 35],
        ];

        // Test descending order
        $sortedResults = $filterCriteria->setData($data)
            ->orderBy(['sort' => 'age', 'operator' => 'desc'])
            ->getResults();

        $expectedResults = [
            ['id' => 2, 'name' => 'Bob', 'age' => 35],
            ['id' => 3, 'name' => 'John', 'age' => 30],
            ['id' => 1, 'name' => 'Alice', 'age' => 25],
        ];

        $this->assertEquals($expectedResults, $sortedResults);
    }

    public function testOrderByAsc()
    {
        $filterCriteria = new FilterCriteria();

        $data = [
            ['id' => 3, 'name' => 'John', 'age' => 30],
            ['id' => 1, 'name' => 'Alice', 'age' => 25],
            ['id' => 2, 'name' => 'Bob', 'age' => 35],
        ];

        // Test ascending order
        $sortedResults = $filterCriteria->setData($data)
            ->orderBy(['sort' => 'age', 'operator' => 'asc'])
            ->getResults();

        $expectedResults = [
            ['id' => 1, 'name' => 'Alice', 'age' => 25],
            ['id' => 3, 'name' => 'John', 'age' => 30],
            ['id' => 2, 'name' => 'Bob', 'age' => 35],
        ];

        $this->assertEquals($expectedResults, $sortedResults);
    }

    public function testPaginate(): void
    {
        $data = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
            ['id' => 3, 'name' => 'Charlie'],
            ['id' => 4, 'name' => 'David'],
            ['id' => 5, 'name' => 'Eve'],
            ['id' => 6, 'name' => 'Frank'],
            ['id' => 7, 'name' => 'Grace'],
            ['id' => 8, 'name' => 'Heidi'],
            ['id' => 9, 'name' => 'Ivan'],
            ['id' => 10, 'name' => 'Judy'],
        ];

        // Paginación con límite de 3 elementos por página y mostrando la página 2
        $limit = 3;
        $page = 2;

        $paginatedResults = $this->filterCriteria->setData($data)
            ->paginate(['page' => $page, 'per_page' => $limit])
            ->getResults();

        // Debería devolver 3 elementos en la página 2
        $this->assertCount(3, $paginatedResults);
        $this->assertEquals(4, $paginatedResults[0]['id']);
        $this->assertEquals(5, $paginatedResults[1]['id']);
        $this->assertEquals(6, $paginatedResults[2]['id']);

        // Paginación con límite de 5 elementos por página y mostrando la página 1
        $limit = 5;
        $page = 1;

        $paginatedResults = $this->filterCriteria->setData($data)
            ->paginate(['page' => $page, 'per_page' => $limit])
            ->getResults();

        // Debería devolver 5 elementos en la página 1
        $this->assertCount(5, $paginatedResults);
        $this->assertEquals(1, $paginatedResults[0]['id']);
        $this->assertEquals(2, $paginatedResults[1]['id']);
        $this->assertEquals(3, $paginatedResults[2]['id']);
        $this->assertEquals(4, $paginatedResults[3]['id']);
        $this->assertEquals(5, $paginatedResults[4]['id']);
    }

}
