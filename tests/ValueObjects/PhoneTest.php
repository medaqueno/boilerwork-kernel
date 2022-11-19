#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Phone;
use Boilerwork\Support\ValueObjects\PhonePrefix;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class PhoneTest extends TestCase
{
    public function providerPhone(): iterable
    {
        yield [
            new Phone(countryCallingCode: PhonePrefix::fromCountryCallingCode('+34'), number: "910837976"),
            new Phone(countryCallingCode: PhonePrefix::fromCountryCallingCode(null), number: "2115684962"),
            new Phone(countryCallingCode: PhonePrefix::fromCountryCallingCode('+49'), number: "2115684962"),
        ];
    }
    /**
     * @test
     * @dataProvider providerPhone
     * @covers \Boilerwork\Support\ValueObjects\Phone
     **/
    public function testNewPhone(Phone $phone): void
    {
        $this->assertInstanceOf(
            Phone::class,
            $phone
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Phone
     **/
    public function testInvalidNumberValue(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('phoneNumber.invalidValue');

        new Phone(countryCallingCode: PhonePrefix::fromCountryCallingCode('+34'), number: "9108 379 76");
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Phone
     **/
    public function testEmptyNumberValue(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('phoneNumber.emptyValue');

        new Phone(countryCallingCode: PhonePrefix::fromCountryCallingCode('+34'), number: "");
    }
}
