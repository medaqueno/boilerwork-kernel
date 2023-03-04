<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Address;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\ValueObjects\Country\Country;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;

final class Address extends ValueObject
{
    private function __construct(
        private Street $street,
        private ?AdministrativeArea $administrativeArea1,
        private ?AdministrativeArea $administrativeArea2,
        private PostalCode $postalCode,
        private Location $location,
        private Country $country,
        private ?Coordinates $coordinates,
    ) {
    }

    public static function fromScalars(
        Street $street,
        ?AdministrativeArea $administrativeArea1,
        ?AdministrativeArea $administrativeArea2,
        PostalCode $postalCode,
        Location $location,
        Country $country,
        ?Coordinates $coordinates,
    ): self {
        return new self(
            street: $street,
            administrativeArea1: $administrativeArea1,
            administrativeArea2: $administrativeArea2,
            postalCode: $postalCode,
            location: $location,
            country: $country,
            coordinates: $coordinates,
        );
    }

    public function street(): Street
    {
        return $this->street;
    }

    public function administrativeArea1(): ?AdministrativeArea
    {
        return $this->administrativeArea1;
    }

    public function administrativeArea2(): ?AdministrativeArea
    {
        return $this->administrativeArea2;
    }

    public function postalCode(): PostalCode
    {
        return $this->postalCode;
    }

    public function location(): Location
    {
        return $this->location;
    }

    public function country(): Country
    {
        return $this->country;
    }

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    public function hasCoordinates(): bool
    {
        return $this->coordinates() !== null;
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street->value(),
            'administrativeArea1' => $this->administrativeArea1 ? $this->administrativeArea1->value() : null,
            'administrativeArea2' => $this->administrativeArea2 ? $this->administrativeArea2->value() : null,
            'postalCode' => $this->postalCode->value(),
            'location' => $this->location->value(),
            'country' => $this->country->value(),
            'coordinates' => $this->coordinates->value(),
        ];
    }
}
