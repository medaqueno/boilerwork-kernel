#!/usr/bin/env php
<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Boilerwork\Support\ValueObjects\Domain\CancellationPolicy;
use Boilerwork\Validation\CustomAssertionFailedException;

final class CancellationPolicyTest extends TestCase
{
    public function providerValidCancellationPolicy(): iterable
    {
        yield 'All' => [
            "amount" => 230.32,
            "iso3" => 'EUR',
            "detail" => 'Cancelacion',
            "dateFrom" => '2022-12-19T15:09:07+00:00'
        ];
        yield 'Null amount' => [
            "amount" => null,
            "iso3" => 'EUR',
            "detail" => 'Cancelacion',
            "dateFrom" => '2022-12-19T15:09:07+00:00'
        ];
        yield 'Null iso3' => [
            "amount" => 230.32,
            "iso3" => null,
            "detail" => 'Cancelacion',
            "dateFrom" => '2022-12-19T15:09:07+00:00'
        ];
    }

    /**
     * @test
     * @dataProvider providerValidCancellationPolicy
     **/
    public function testValidCancellationPolicy($amount, $iso3, $detail, $dateFrom): void
    {
        $cancellationPolicy = CancellationPolicy::fromScalars($amount, $iso3, $detail, $dateFrom);
        $this->assertInstanceOf(
            CancellationPolicy::class,
            $cancellationPolicy
        );
    }

    public function providerInvalidCancellationPolicy(): iterable
    {
        yield 'Invalid DateTime' => [
            "amount" => 230.32,
            "iso3" => 'EUR',
            "detail" => 'Cancelacion',
            "dateFrom" => 'ddd',
            "except" => 'Exception',
        ];

        yield 'Empty detail' => [
            "amount" => 230.32,
            "iso3" => 'EUR',
            "detail" => '',
            "dateFrom" => '2022-12-19 15:09:07+00',
            "except" => CustomAssertionFailedException::class,
        ];
    }

    /**
     * @test
     * @dataProvider providerInvalidCancellationPolicy
     **/
    public function testInvalidCancellationPolicy($amount, $iso3, $detail, $dateFrom, $except): void
    {
        $this->expectException($except);
        CancellationPolicy::fromScalars($amount, $iso3, $detail, $dateFrom);
    }

    /**
     * @test
     * @dataProvider providerValidCancellationPolicy
     */
    public function testToArray($amount, $iso3, $detail, $dateFrom): void
    {
        $cancellationPolicy = CancellationPolicy::fromScalars($amount, $iso3, $detail, $dateFrom);
        $toArrayResult = $cancellationPolicy->toArray();

        $this->assertIsArray($toArrayResult);
        $this->assertArrayHasKey('price', $toArrayResult);
        $this->assertArrayHasKey('detail', $toArrayResult);
        $this->assertArrayHasKey('dateFrom', $toArrayResult);

        if ($amount !== null && $iso3 !== null) {
            $this->assertIsArray($toArrayResult['price']);
            $this->assertEquals($amount, $toArrayResult['price']['amount']);
            $this->assertEquals($iso3, $toArrayResult['price']['symbol']['iso3']);
        } else {
            $this->assertNull($toArrayResult['price']);
        }

        $this->assertEquals($detail, $toArrayResult['detail']);

        if ($dateFrom !== null) {
            $this->assertEquals($dateFrom, $toArrayResult['dateFrom']);
        } else {
            $this->assertNull($toArrayResult['dateFrom']);
        }
    }


}