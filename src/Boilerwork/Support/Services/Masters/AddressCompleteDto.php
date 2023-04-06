<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters;

use Boilerwork\Support\Services\Masters\Countries\CountryEntity;
use Boilerwork\Support\Services\Masters\Locations\LocationEntity;
use Boilerwork\Support\ValueObjects\Geo\Address\Address;
use Boilerwork\Support\ValueObjects\Geo\Country\Country;
use Boilerwork\Support\ValueObjects\Geo\Location;
use Boilerwork\Support\ValueObjects\Identity;
use Boilerwork\Support\ValueObjects\Language\Language;

readonly class AddressCompleteDto
{
    private function __construct(
        private Address $address,
        private LocationEntity $location,
        private CountryEntity $country,
    ) {
    }

    public static function fromObjects(
        Address $address,
        LocationEntity $location,
        CountryEntity $country,
    ): self {
        return new self(address: $address, location: $location, country: $country);
    }

    public static function fromJson(string $json, string $lang = Language::FALLBACK): self
    {
        $data = json_decode($json, true);

        $addressData     = $data['address'] ?? [];
        $streetData      = $addressData['street'] ?? [];
        $coordinatesData = $addressData['coordinates'] ?? null;
        $address         = Address::fromScalars(
            $streetData['name'],
            $streetData['number'] ?? null,
            $streetData['other1'] ?? null,
            $streetData['other2'] ?? null,
            $addressData['administrativeArea1'] ?? null,
            $addressData['administrativeArea2'] ?? null,
            $addressData['postalCode'] ?? null,
            $coordinatesData['latitude'] ?? null,
            $coordinatesData['longitude'] ?? null,
        );

        $locationData            = $data['location'];
        $locationCoordinatesData = $locationData['coordinates'] ?? null;
        $location                = new LocationEntity(
            Identity::fromString($locationData['id']),
            Location::fromScalars(
                [$lang ?? Language::FALLBACK => $locationData['name'][$lang] ?? $locationData['name'][Language::FALLBACK]],
                $locationData['iso31661Alpha2'],
                $locationCoordinatesData['latitude'] ?? null,
                $locationCoordinatesData['longitude'] ?? null,
            )
        );

        $countryData            = $data['country'];
        $countryCoordinatesData = $countryData['coordinates'] ?? null;
        $country                = new CountryEntity(
            Identity::fromString($countryData['id']),
            Country::fromScalarsWithIso31661Alpha2(
                [$lang ?? Language::FALLBACK => $countryData['name'][$lang] ?? $countryData['name'][Language::FALLBACK]],
                $countryData['iso31661Alpha2'] ?? '',
                $countryCoordinatesData['latitude'] ?? null,
                $countryCoordinatesData['longitude'] ?? null,
            )
        );

        return new self(address: $address, location: $location, country: $country);
    }


    public function address(): Address
    {
        return $this->address;
    }

    public function location(): LocationEntity
    {
        return $this->location;
    }

    public function country(): CountryEntity
    {
        return $this->country;
    }

    /**
     * @return array{
     *     address: array{
     *         street: array{name: string, number: string|null, other1: string|null, other2: string|null},
     *         administrativeArea1: string|null,
     *         administrativeArea2: string|null,
     *         postalCode: string|null,
     *         coordinates: array{latitude: float, longitude: float}|null
     *     },
     *     location: array{
     *         name: string|null,
     *         iso31661Alpha2: string,
     *         coordinates: array{latitude: float, longitude: float}
     *     },
     *     country: array{
     *         name: string|null,
     *         iso31661Alpha2: string|null,
     *         iso31661Alpha3: string|null,
     *         coordinates: array{latitude: float, longitude: float}|null
     *     }
     * }
     *
     * @see Address::toArray()
     * @see Location::toArray()
     * @see Country::toArray()
     */
    public function toArray(): array
    {
        return [
            'address'  => $this->address()->toArray(),
            'location' => $this->location()->toArray(),
            'country'  => $this->country()->toArray(),
        ];
    }
}
