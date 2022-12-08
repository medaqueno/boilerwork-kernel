#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Persistence\QueryBuilder\PostFilterDto;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class PostFilterDtoTest extends TestCase
{
    public function providerPostFilterDto(): iterable
    {
        yield [
            PostFilterDto::create(
                params: 'category=3-4;price=min-300;facilities=1;facilities=4;facilities=6;category=1-6',
                acceptedParams: ['category', 'price', 'facilities']
            ),
        ];
    }

    /**
     * @test
     * @dataProvider providerPostFilterDto
     * @covers \Boilerwork\Persistence\QueryBuilder\PostFilterDto
     **/
    public function testCreateCriteriaDto(PostFilterDto $postFilterDto): void
    {
        $this->assertInstanceOf(
            PostFilterDto::class,
            $postFilterDto
        );

        $this->assertArrayHasKey('category', $postFilterDto->filters());
        $this->assertArrayHasKey('price', $postFilterDto->filters());
        $this->assertArrayHasKey('facilities', $postFilterDto->filters());

        $this->assertContains('3-4', $postFilterDto->filters()['category']);
        $this->assertContains('1-6', $postFilterDto->filters()['category']);
        $this->assertContains('1', $postFilterDto->filters()['facilities']);
        $this->assertContains('4', $postFilterDto->filters()['facilities']);
        $this->assertContains('6', $postFilterDto->filters()['facilities']);
        $this->assertContains('min-300', $postFilterDto->filters()['price']);
    }

    /**
     * @test
     * @covers \Boilerwork\Persistence\QueryBuilder\PostFilterDto
     **/
    public function testNotAllowedValue(): void
    {
        $postFilterDto = PostFilterDto::create(
            params: 'category=3-4;price=min-300;facilities=1;facilities=4;facilities=6;category=1-6',
            acceptedParams: ['category', 'price']
        );

        $this->assertArrayNotHasKey('facilities', $postFilterDto->filters());
    }
}
