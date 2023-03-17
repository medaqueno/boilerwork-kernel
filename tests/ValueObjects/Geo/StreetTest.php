<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Geo\Address\Street;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Boilerwork\Support\ValueObjects\Geo\Address\Street
 */
class StreetTest extends TestCase
{
    public function testFromScalars(): void
    {
        $name = 'Fake St';
        $number = '123';
        $other1 = 'Unit 1';
        $other2 = 'Floor 2';
        $street = Street::fromScalars($name, $number, $other1, $other2);

        $this->assertInstanceOf(Street::class, $street);
        $this->assertEquals($name, $street->name());
        $this->assertEquals($number, $street->number());
        $this->assertEquals($other1, $street->other1());
        $this->assertEquals($other2, $street->other2());
    }

    public function testEmptyNameThrowsException(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('Name must not be empty');

        Street::fromScalars('', null, null, null);
    }

    public function testInvalidNameTypeThrowsException(): void
    {
        $this->expectException(TypeError::class);

        Street::fromScalars(null, null, null, null);
    }

    public function testToArray(): void
    {
        $name = 'Fake St';
        $number = '123';
        $other1 = 'Unit 1';
        $other2 = 'Floor 2';
        $street = Street::fromScalars($name, $number, $other1, $other2);

        $expected = [
            'name' => $name,
            'number' => $number,
            'other1' => $other1,
            'other2' => $other2
        ];

        $this->assertEquals($expected, $street->toArray());
    }

    public function testToString(): void
    {
        $name = 'Fake St';
        $number = '123';
        $other1 = 'Unit 1';
        $other2 = 'Floor 2';
        $street = Street::fromScalars($name, $number, $other1, $other2);

        $expected = sprintf('%s %s, %s %s', $name, $number, $other1, $other2);
        $this->assertEquals($expected, $street->toString());
    }
}
