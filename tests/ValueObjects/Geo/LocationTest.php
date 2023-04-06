<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo\Tests;

use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Location;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    public function testFromScalars(): void
    {
        $location = Location::fromScalars(
            ['EN' => 'Test Location', 'ES' => 'Ubicación de prueba'],
            'US',
            37.7749,
            -122.4194
        );

        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame('Test Location', $location->nameByLanguage('EN'));
        $this->assertSame('Ubicación de prueba', $location->nameByLanguage('ES'));
        $this->assertSame('US', $location->iso31661Alpha2()->toString());
        $this->assertEquals(new Coordinates(37.7749, -122.4194), $location->coordinates());
    }

    public function testToArray(): void
    {
        $location = Location::fromScalars(
            ['EN' => 'Test Location', 'ES' => 'Ubicación de prueba'],
            'US',
            37.7749,
            -122.4194
        );

        $expected = [
            'name' => 'Test Location',
            'iso31661Alpha2' => 'US',
            'coordinates' => [
                'latitude' => 37.7749,
                'longitude' => -122.4194,
            ],
        ];

        $this->assertSame($expected, $location->toArray());
    }

    public function testInvalidIso31661Alpha2(): void
    {
        $this->expectExceptionMessage('The value you have entered is not a valid ISO 3166-1 alpha-2 country code.');
        $this->expectExceptionMessage('iso31661Alpha2.invalid');

        Location::fromScalars(
            ['EN' => 'Test Location', 'ES' => 'Ubicación de prueba'],
            'ZZ', // Invalid ISO 3166-1 alpha-2 code
            37.7749,
            -122.4194
        );
    }

    public function testInvalidLanguage(): void
    {
        $this->expectExceptionMessage('Language must be: EN,ES');
        $this->expectExceptionMessage('language.invalidIso3166Alpha2');

        Location::fromScalars(
            ['EN' => 'Test Location', 'FR' => 'Emplacement de test'], // Invalid language code
            'US',
            37.7749,
            -122.4194
        );
    }

    public function testEmptyText(): void
    {
        $this->expectExceptionMessage('Texts must not be empty');
        $this->expectExceptionMessage('text.notEmpty');

        Location::fromScalars(
            ['EN' => 'Test Location', 'ES' => ''], // Empty text
            'US',
            37.7749,
            -122.4194
        );
    }
}
