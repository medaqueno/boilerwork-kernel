<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters;

use Boilerwork\Support\Services\Masters\Countries\CountryEntity;
use Boilerwork\Support\Services\Masters\Locations\LocationEntity;
use Boilerwork\Support\ValueObjects\Geo\Address\Address;

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
     *         name: string,
     *         iso31661Alpha2: string,
     *         coordinates: array{latitude: float, longitude: float}
     *     },
     *     country: array{
     *         name: string,
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
            'address' => $this->address()->toArray(),
            'location' => $this->location()->toArray(),
            'country' => $this->country()->toArray(),
        ];
    }
}
