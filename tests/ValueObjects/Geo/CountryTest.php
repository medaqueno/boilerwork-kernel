<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo\Country\Tests;

use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Country\Country;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha3;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Geo\Country\Country
     */
    public function testFromScalars(): void
    {
        $country = Country::fromScalars(
            ['EN' => 'United States', 'ES' => 'Estados Unidos'],
            'US',
            'USA',
            37.7749,
            -122.4194
        );

        $this->assertInstanceOf(Country::class, $country);
        $this->assertInstanceOf(MultiLingualText::class, $country->names());
        $this->assertInstanceOf(Iso31661Alpha2::class, $country->iso31661Alpha2());
        $this->assertInstanceOf(Iso31661Alpha3::class, $country->iso31661Alpha3());
        $this->assertInstanceOf(Coordinates::class, $country->coordinates());
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Geo\Country\Country
     */
    public function testFromScalarsWithIso31661Alpha2(): void
    {
        $country = Country::fromScalarsWithIso31661Alpha2(
            ['EN' => 'United States', 'ES' => 'Estados Unidos'],
            'US',
            37.7749,
            -122.4194
        );

        $this->assertInstanceOf(Country::class, $country);
        $this->assertInstanceOf(MultiLingualText::class, $country->names());
        $this->assertInstanceOf(Iso31661Alpha2::class, $country->iso31661Alpha2());
        $this->assertNull($country->iso31661Alpha3());
        $this->assertInstanceOf(Coordinates::class, $country->coordinates());
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Geo\Country\Country
     */
    public function testFromScalarsWithIso31661Alpha3(): void
    {
        $country = Country::fromScalarsWithIso31661Alpha3(
            ['EN' => 'United States', 'ES' => 'Estados Unidos'],
            'USA',
            37.7749,
            -122.4194
        );

        $this->assertInstanceOf(Country::class, $country);
        $this->assertInstanceOf(MultiLingualText::class, $country->names());
        $this->assertNull($country->iso31661Alpha2());
        $this->assertInstanceOf(Iso31661Alpha3::class, $country->iso31661Alpha3());
        $this->assertInstanceOf(Coordinates::class, $country->coordinates());
    }

    public function testInvalidIsoCodes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must not be empty');
        $this->expectExceptionMessage('iso31661Alpha2.notEmpty');

        Country::fromScalars(
            ['EN' => 'Invalid Country', 'ES' => 'País inválido'],
            '',
            '',
            0,
            0
        );
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Geo\Country\Country
     */
    public function testToString(): void
    {
        $country = Country::fromScalars(
            ['EN' => 'United States', 'ES' => 'Estados Unidos'],
            'US',
            'USA',
            37.7749,
            -122.4194
        );

        $this->assertSame('Estados Unidos', $country->toString());
        $this->assertSame('United States', $country->toString('EN'));
    }

    /**
     * @test
     * @covers \Boilerwork\Support\ValueObjects\Geo\Country\Country
     */
    public function testToArray(): void
    {
        $country = Country::fromScalars(
            ['EN' => 'United States', 'ES' => 'Estados Unidos'],
            'US',
            'USA',
            37.7749,
            -122.4194
        );

        $expectedArray = [
            'name' => 'Estados Unidos',
            'iso31661Alpha2' => 'US',
            'iso31661Alpha3' => 'USA',
            'coordinates' => [
                'latitude' => 37.7749,
                'longitude' => -122.4194,
            ],
        ];

        $this->assertSame($expectedArray, $country->toArray());
        $this->assertSame('Estados Unidos', $country->toArray()['name']);
        $this->assertSame('US', $country->toArray()['iso31661Alpha2']);
        $this->assertSame('USA', $country->toArray()['iso31661Alpha3']);
        $this->assertSame(37.7749, $country->toArray()['coordinates']['latitude']);
        $this->assertSame(-122.4194, $country->toArray()['coordinates']['longitude']);
    }
}
