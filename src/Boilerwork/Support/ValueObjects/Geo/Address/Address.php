<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo\Address;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Validation\Assert;

final class Address extends ValueObject
{
    private function __construct(
        private Street $street,
        private ?AdministrativeArea $administrativeArea1,
        private ?AdministrativeArea $administrativeArea2,
        private ?PostalCode $postalCode,
        private ?Coordinates $coordinates,
    ) {
    }

    public static function fromScalars(
        string $streetName,
        ?string $streetNumber,
        ?string $streetOther1,
        ?string $streetOther2,
        ?string $administrativeArea1,
        ?string $administrativeArea2,
        ?string $postalCode,
        ?float $latitude,
        ?float $longitude,
    ): self {
        return new self(
            street: Street::fromScalars(
                name: $streetName,
                number: $streetNumber,
                other1: $streetOther1,
                other2: $streetOther2
            ),
            administrativeArea1: $administrativeArea1 ? AdministrativeArea::fromString($administrativeArea1) : null,
            administrativeArea2: $administrativeArea2 ? AdministrativeArea::fromString($administrativeArea2) : null,
            postalCode: $postalCode ? PostalCode::fromString($postalCode) : null,
            coordinates: $latitude && $longitude ? Coordinates::fromScalars($latitude, $longitude) : null,
        );
    }

    public function toString(): string
    {
        return sprintf(
            '%s %s %s %s',
            $this->street->toString(),
            $this->administrativeArea1 ? $this->administrativeArea1->toString() : '',
            $this->administrativeArea2 ? $this->administrativeArea2->toString() : '',
            $this->postalCode ? $this->postalCode->toString() : ''
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

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street->toArray(),
            'administrative_area_1' => $this->administrativeArea1 ? $this->administrativeArea1->toString() : null,
            'administrative_area_2' => $this->administrativeArea2 ? $this->administrativeArea2->toString() : null,
            'postal_code' => $this->postalCode ? $this->postalCode->toString() : null,
            'coordinates' => $this->coordinates ? $this->coordinates->toArray() : null,
        ];
    }
}
