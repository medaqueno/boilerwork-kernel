#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\PhoneNumber;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class PhoneNumberTest extends TestCase
{
    public function providerPhoneNumber(): iterable
    {
        yield [
            new PhoneNumber("910837976"),
            new PhoneNumber(number: "2115684962"),
            new PhoneNumber(number: "2115684962"),
        ];
    }
    /**
     * @test
     * @dataProvider providerPhoneNumber
     * @covers \Boilerwork\Support\ValueObjects\PhoneNumber
     **/
    public function testNewPhone(PhoneNumber $phone): void
    {
        $this->assertInstanceOf(
            PhoneNumber::class,
            $phone
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\PhoneNumber
     **/
    public function testInvalidNumberValue(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('phoneNumber.invalidValue');

        new PhoneNumber("9108 379 76");
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\PhoneNumber
     **/
    public function testEmptyNumberValue(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('phoneNumber.emptyValue');

        new PhoneNumber("");
    }
}
