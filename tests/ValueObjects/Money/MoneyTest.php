#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Money\Currency;
use Boilerwork\Support\ValueObjects\Money\Money;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class MoneyTest extends TestCase
{
    public function providerMoney(): iterable
    {
        yield [
            $this->getMockForAbstractClass(
                Money::class,
                [120, new Currency('EUR')]
            ),
            $this->getMockForAbstractClass(
                Money::class,
                [120.567, new Currency('USD')]
            ),
        ];
    }

    /**
     * @test
     * @dataProvider providerMoney
     * @covers \Boilerwork\Support\ValueObjects\Money
     **/
    public function testMoney(Money $money): void
    {
        $this->assertInstanceOf(
            Money::class,
            $money
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Money
     **/
    public function testRoundMoney(): void
    {
        $money =  $this->getMockForAbstractClass(
            Money::class,
            [120.567, new Currency('USD')]
        );

        $this->assertSame(120.57, $money->rounded());
    }
}
