<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Geo\Location;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Boilerwork\Support\ValueObjects\Geo\Location;
 */

class LocationTest extends TestCase
{
    public function testFromScalars(): void
    {
        $name = 'Test Location';
        $latitude = 12.34;
        $longitude = 56.78;

        $location = Location::fromScalars($name, $latitude, $longitude);

        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame($name, $location->name());
        $this->assertInstanceOf(Coordinates::class, $location->coordinates());
        $this->assertSame($latitude, $location->coordinates()->latitude());
        $this->assertSame($longitude, $location->coordinates()->longitude());
    }

    public function testInvalidName(): void
    {
        $this->expectException(CustomAssertionFailedException::class);
        $this->expectExceptionMessage('location.invalidName');

        Location::fromScalars('', 12.34, 56.78);
    }

    public function testToArray(): void
    {
        $name = 'Test Location';
        $latitude = 12.34;
        $longitude = 56.78;

        $location = Location::fromScalars($name, $latitude, $longitude);
        $arrayRepresentation = $location->toArray();

        $this->assertIsArray($arrayRepresentation);
        $this->assertCount(2, $arrayRepresentation);
        $this->assertArrayHasKey('name', $arrayRepresentation);
        $this->assertArrayHasKey('coordinates', $arrayRepresentation);
        $this->assertSame($name, $arrayRepresentation['name']);
        $this->assertSame(
            [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
            $arrayRepresentation['coordinates']
        );
    }
}
