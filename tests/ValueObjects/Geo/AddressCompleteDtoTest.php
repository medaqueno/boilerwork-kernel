<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Tests;

use Boilerwork\Support\Services\Masters\Countries\CountryEntity;
use Boilerwork\Support\Services\Masters\Locations\LocationEntity;
use Boilerwork\Support\ValueObjects\Geo\Address\Address;
use Boilerwork\Support\ValueObjects\Geo\Country\Country;
use Boilerwork\Support\ValueObjects\Geo\Location;
use Boilerwork\Support\ValueObjects\Identity;
use Boilerwork\Support\ValueObjects\Language\Language;
use Boilerwork\Support\Services\Masters\AddressCompleteDto;
use PHPUnit\Framework\TestCase;

class AddressCompleteDtoTest extends TestCase
{
    public function testFromObjects(): void
    {
        $address = Address::fromScalars('Main St', '123');
        $location = new LocationEntity(
            Identity::create(),
            Location::fromScalars(['ES' => 'Test Location'], 'US', 37.7749, -122.4194)
        );
        $country = new CountryEntity(
            Identity::create(),
            Country::fromScalarsWithIso31661Alpha2(['ES' => 'United States'], 'US', 37.7749, -122.4194)
        );

        $addressCompleteDto = AddressCompleteDto::fromObjects($address, $location, $country);

        $this->assertInstanceOf(AddressCompleteDto::class, $addressCompleteDto);
        $this->assertSame($address, $addressCompleteDto->address());
        $this->assertSame($location, $addressCompleteDto->location());
        $this->assertSame($country, $addressCompleteDto->country());
    }

    public function testFromJson(): void
    {
        $json = json_encode([
            'address' => [
                'street' => [
                    'name' => 'Main St',
                    'number' => '123',
                ],
                'administrativeArea1' => 'California',
                'administrativeArea2' => 'San Francisco',
                'postalCode' => '94107',
                'coordinates' => [
                    'latitude' => 37.7749,
                    'longitude' => -122.4194,
                ],
            ],
            'location' => [
                'id' => '12345678-1234-5678-1234-567812345678',
                'name' => ['ES' => 'Test Location'],
                'iso31661Alpha2' => 'US',
                'coordinates' => [
                    'latitude' => 37.7749,
                    'longitude' => -122.4194,
                ],
            ],
            'country' => [
                'id' => '12345678-1234-5678-1234-567812345679',
                'name' => ['ES' => 'United States'],
                'iso31661Alpha2' => 'US',
                'coordinates' => [
                    'latitude' => 37.7749,
                    'longitude' => -122.4194,
                ],
            ],
        ]);

        $addressCompleteDto = AddressCompleteDto::fromJson($json, 'ES');

        $this->assertInstanceOf(AddressCompleteDto::class, $addressCompleteDto);
        $this->assertEquals('Main St', $addressCompleteDto->address()->street()->name());
        $this->assertEquals('123', $addressCompleteDto->address()->street()->number());
        $this->assertEquals('Test Location', $addressCompleteDto->location()->location->name());
        $this->assertEquals('US', $addressCompleteDto->location()->location->iso31661Alpha2()->toString());
        $this->assertEquals('United States', $addressCompleteDto->country()->country->name());
        $this->assertEquals('US', $addressCompleteDto->country()->country->iso31661Alpha2()->toString());
    }

    public function testToArray(): void
    {
        $locationId = Identity::create();
        $countryId = Identity::create();
        $address = Address::fromScalars('Main St', '123');
        $location = new LocationEntity(
            $locationId,
            Location::fromScalars(['ES' => 'Test Location'], 'US', 37.7749, -122.4194)
        );
        $country = new CountryEntity(
            $countryId,
            Country::fromScalarsWithIso31661Alpha2(['ES' => 'United States'], 'US', 37.7749, -122.4194)
        );

        $addressCompleteDto = AddressCompleteDto::fromObjects($address, $location, $country);
        $arrayRepresentation = $addressCompleteDto->toArray();

        $expected = [
            'address' => [
                'street' => [
                    'name' => 'Main St',
                    'number' => '123',
                    'other1' => null,
                    'other2' => null,
                ],
                'administrativeArea1' => null,
                'administrativeArea2' => null,
                'postalCode' => null,
                'coordinates' => null,
            ],
            'location' => [
                'id' => $locationId->toString(),
                'iso31661Alpha2' => 'US',
                'name' => 'Test Location',
                'coordinates' => [
                    'latitude' => 37.7749,
                    'longitude' => -122.4194,
                ],
            ],
            'country' => [
                'id' => $countryId->toString(),
                'name' => 'United States',
                'iso31661Alpha2' => 'US',
                'coordinates' => [
                    'latitude' => 37.7749,
                    'longitude' => -122.4194,
                ],
            ],
        ];

        $this->assertSame($expected, $arrayRepresentation);
    }
}
