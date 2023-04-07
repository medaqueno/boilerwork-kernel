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

use function json_decode;
use function json_encode;

use const JSON_FORCE_OBJECT;
use const JSON_UNESCAPED_UNICODE;

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

        $locationData = $data['location'];

        $locationCoordinatesData = $locationData['coordinates'] ?? null;
        $location                = new LocationEntity(
            Identity::fromString($locationData['id']),
            Location::fromScalars(
                $locationData['name'],
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
                $countryData['name'],
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
     *
     * @return array{
     * address: {
     *  street: {
     *      name: array,
     *      number: null|string,
     *      other1: null|string,
     *      other2: null|string
     *  },
     *  administrative_area1: null|string,
     *  administrative_area2: null|string,
     *  postal_code: null|string,
     *  coordinates: {
     *      latitude: float,
     *      longitude: float
     *  }?null
     *  },
     * location: {
     *  id: string,
     *  name: array,
     *  iso31661alpha2: string,
     *  coordinates: {
     *      latitude: float,
     *      longitude: float
     *  }?null
     *  },
     * country: {
     *  id: string,
     *  name: array,
     *  iso31661alpha2: string,
     *  iso31661alpha3: null|string,
     *  coordinates: {
     *      latitude: float,
     *      longitude: float
     *   }?null
     *  }
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

    /**
     *
     * @return array{
     * address: {
     *  street: {
     *      name: string,
     *      number: null|string,
     *      other1: null|string,
     *      other2: null|string
     *  },
     *  administrative_area1: null|string,
     *  administrative_area2: null|string,
     *  postal_code: null|string,
     *  coordinates: {
     *      latitude: float,
     *      longitude: float
     *  }?null
     *  },
     * location: {
     *  id: string,
     *  name: string,
     *  iso31661alpha2: string,
     *  coordinates: {
     *      latitude: float,
     *      longitude: float
     *  }?null
     *  },
     * country: {
     *  id: string,
     *  name: string,
     *  iso31661alpha2: string,
     *  iso31661alpha3: null|string,
     *  coordinates: {
     *      latitude: float,
     *      longitude: float
     *   }?null
     *  }
     * }
     */
    public function toArrayInLang(?string $lang): array
    {
        return [
            'address'  => $this->address()->toArray(),
            'location' => $this->location()->toArray($lang),
            'country'  => $this->country()->toArray($lang),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }
}