#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Phone;
use Boilerwork\Support\ValueObjects\PhoneNumber;
use Boilerwork\Support\ValueObjects\PhonePrefix;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class PhoneTest extends TestCase
{
    public function providerPhone(): iterable
    {
        yield [
            $this->getMockForAbstractClass(
                Phone::class,
                [PhonePrefix::fromCountryCallingCode('+34'), new PhoneNumber("910837976")]
            )
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

        $this->assertEquals('910 83 79 76', $phone->formatNational(), 'Invalid Phone National Format');
        $this->assertEquals('+34910837976', $phone->toPrimitive(), 'Invalid Phone Primitive Format');
        $this->assertEquals('+34 910 83 79 76', $phone->formatInternational(), 'Invalid Phone International Format');
        $this->assertEquals('+34', $phone->countryCallingCode()->toPrimitive(), 'Invalid countryCallingCode value');
        $this->assertEquals('910837976', $phone->number()->toPrimitive(), 'Invalid countryCallingCode value');
    }
}
