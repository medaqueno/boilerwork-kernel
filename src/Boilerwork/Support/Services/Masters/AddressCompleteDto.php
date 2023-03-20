<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters;

use Boilerwork\Support\Services\Masters\Countries\CountryDto;
use Boilerwork\Support\Services\Masters\Locations\LocationDto;

readonly class AddressCompleteDto
{
    private function __construct(
        private AddressDto $address,
        private LocationDto $location,
        private CountryDto $country,
    ) {
    }

    public static function fromObjects(
        AddressDto $address,
        LocationDto $location,
        CountryDto $country,
    ): self {
        return new self(address: $address, location: $location, country: $country);
    }

    public function address(): AddressDto
    {
        return $this->address;
    }

    public function location(): LocationDto
    {
        return $this->location;
    }

    public function country(): CountryDto
    {
        return $this->country;
    }

    /**
     * @return array{
     *     address: array{
     *         administrativeArea1: null|string,
     *         administrativeArea2: null|string,
     *         coordinates: array{
     *             latitude: float,
     *             longitude: float
     *         }|null,
     *         postalCode: null|string,
     *         street: array{
     *             name: string,
     *             number: null|string,
     *             other1: null|string,
     *             other2: null|string
     *         }
     *     },
     *     country: array{
     *         id: string,
     *         isoAlpha2: string,
     *         name: array<string,string>
     *     },
     *     location: array{
     *         coordinates: array{
     *             latitude: float,
     *             longitude: float
     *         },
     *         id: string,
     *         isoAlpha2: string,
     *         name: array<string,string>
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
