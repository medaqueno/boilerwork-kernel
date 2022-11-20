#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Money\Currency;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class CurrencyTest extends TestCase
{
    public function providerCurrency(): iterable
    {
        yield [
            new Currency('EUR'),
        ];
    }

    /**
     * @test
     * @dataProvider providerCurrency
     * @covers \Boilerwork\Support\ValueObjects\Money\Currency
     **/
    public function testCurrency(Currency $currency): void
    {
        $this->assertInstanceOf(
            Currency::class,
            $currency
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Money\Currency
     **/
    public function testInvalidCurrencyValue(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('currency.invalidValue');

        new Currency('BAD');
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Money\Currency
     **/
    public function testEmptyCurrencyValue(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('currency.notEmpty');

        new Currency('');
    }
}
