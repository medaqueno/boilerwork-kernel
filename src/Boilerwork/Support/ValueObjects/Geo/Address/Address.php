<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo\Address;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Validation\Assert;

class Address extends ValueObject
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
        ?string $streetNumber = null,
        ?string $streetOther1 = null,
        ?string $streetOther2 = null,
        ?string $administrativeArea1 = null,
        ?string $administrativeArea2 = null,
        ?string $postalCode = null,
        ?float $latitude = null,
        ?float $longitude = null,
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
            "%s %s %s %s",
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


    public function postalCode(): ?PostalCode
    {
        return $this->postalCode;
    }

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @return array{
     *     street: array{name: string, number: string|null, other1: string|null, other2: string|null},
     *     administrativeArea1: string|null,
     *     administrativeArea2: string|null,
     *     postalCode: string|null,
     *     coordinates: array{ latitude: float, longitude: float }|null
     * }
     */
    public function toArray(): array
    {
        return [
            'street' => $this->street->toArray(),
            'administrativeArea1' => $this->administrativeArea1?->toString(),
            'administrativeArea2' => $this->administrativeArea2?->toString(),
            'postalCode' => $this->postalCode?->toString(),
            'coordinates' => $this->coordinates?->toArray(),
        ];
    }
}
