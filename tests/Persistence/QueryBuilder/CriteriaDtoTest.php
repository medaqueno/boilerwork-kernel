#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Persistence\QueryBuilder\CriteriaDto;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class CriteriaDtoTest extends TestCase
{
    public function providerCriteriaDto(): iterable
    {
        yield [
            CriteriaDto::create(
                params: [
                    'id' => '5789F9AF-BE4C-4CD0-9B4B-16A05CE26BF3',
                    'name' => 'Name',
                    'region' => 'eu',
                ],
                orderBy: 'id,ASC'
            ),
            CriteriaDto::create(
                params: [
                    'id' => '5789F9AF-BE4C-4CD0-9B4B-16A05CE26BF3',
                    'name' => 'Name',
                    'region' => 'eu',
                ]
            ),
            CriteriaDto::create(
                params: ['id' => null],
                orderBy: 'id,desc'
            ),
        ];
    }

    /**
     * @test
     * @covers \Boilerwork\Persistence\QueryBuilder\CriteriaDto
     **/
    public function testCriteriaDtoValuesOk(): void
    {
        $criteriaDto = CriteriaDto::create(
            params: [
                'id' => null,
                'name' => 'Name',
                'region' => 'eu',
            ],
            orderBy: 'id,ASC'
        );

        $this->assertArrayHasKey('id', $criteriaDto->params());
        $this->assertArrayHasKey('name', $criteriaDto->params());
        $this->assertArrayHasKey('region', $criteriaDto->params());
        $this->assertEquals($criteriaDto->params()['name'], 'Name');
        $this->assertEquals($criteriaDto->orderBy()['sort'], 'id');
        $this->assertEquals($criteriaDto->orderBy()['operator'], 'ASC');
    }

    /**
     * @test
     * @dataProvider providerCriteriaDto
     * @covers \Boilerwork\Persistence\QueryBuilder\CriteriaDto
     **/
    public function testCreateCriteriaDto($criteriaDto): void
    {
        $this->assertInstanceOf(
            CriteriaDto::class,
            $criteriaDto
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Persistence\QueryBuilder\CriteriaDto
     **/
    public function testOrderByNonAllowedValue(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('criteriaSortValue.notAllowed');

        CriteriaDto::create(
            params: ['id' => '5789F9AF-BE4C-4CD0-9B4B-16A05CE26BF3',],
            orderBy: 'name,ASC'
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Persistence\QueryBuilder\CriteriaDto
     **/
    public function testOrderByInvalidValue(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('criteriaOrderBy.invalidValue');

        CriteriaDto::create(
            params: ['id' => '5789F9AF-BE4C-4CD0-9B4B-16A05CE26BF3',],
            orderBy: 'name,OTHER'
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Persistence\QueryBuilder\CriteriaDto
     **/
    public function testOrderByInvalidFormat(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('criteriaOrderBy.invalidValue');

        CriteriaDto::create(
            params: ['id' => '5789F9AF-BE4C-4CD0-9B4B-16A05CE26BF3',],
            orderBy: 'name-DESC'
        );
    }
}
