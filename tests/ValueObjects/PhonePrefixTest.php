#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\PhonePrefix;
use PHPUnit\Framework\TestCase;
use Boilerwork\Validation\CustomAssertionFailedException;
// use Deminy\Counit\TestCase;

final class PhonePrefixTest extends TestCase
{

    public function PhonePrefixProvider(): iterable
    {
        yield [
            PhonePrefix::fromCountryCallingCode('44'),
            PhonePrefix::fromCountryCallingCode('+34'),
            PhonePrefix::fromCountryCallingCode(''),
            PhonePrefix::fromCountryCallingCode(null),
            PhonePrefix::fromIso31662('GB'),
            PhonePrefix::fromIso31662(''),
            PhonePrefix::fromIso31662(null),
        ];
    }

    /**
     * @test
     * @dataProvider PhonePrefixProvider
     * @covers \Boilerwork\Support\ValueObjects\PhonePrefix
     **/
    public function testPhonePrefix(PhonePrefix $phonePrefix): void
    {
        $this->assertInstanceOf(
            PhonePrefix::class,
            $phonePrefix
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\PhonePrefix
     **/
    public function testInvalidCountryCallingCodeFormat(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('phonePrefix.invalidFormat');

        PhonePrefix::fromCountryCallingCode('94.564');
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\PhonePrefix
     **/
    public function testInvalidCountryCallingCodeValue(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('phonePrefix.invalidValue');

        PhonePrefix::fromCountryCallingCode('982');
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\PhonePrefix
     **/
    public function testInvalidIso31662(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('phonePrefix.invalidValue');

        PhonePrefix::fromIso31662('LP');
    }
}
