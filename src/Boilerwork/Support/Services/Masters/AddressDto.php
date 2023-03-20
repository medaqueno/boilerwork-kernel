#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters;

use Boilerwork\Support\ValueObjects\Geo\Coordinates;

readonly class AddressDto
{
    private function __construct(
        public string $streetName,
        public ?string $streetNumber,
        public ?string $streetOther1,
        public ?string $streetOther2,
        public ?string $administrativeArea1,
        public ?string $administrativeArea2,
        public ?string $postalCode,
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
            $streetName,
            $streetNumber,
            $streetOther1,
            $streetOther2,
            $administrativeArea1,
            $administrativeArea2,
            $postalCode,
            Coordinates::fromScalars($latitude,$longitude),
        );
    }

    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function toArray(): array
    {
        return [
            'street'              => [
                'name'   => $this->streetName,
                'number' => $this->streetNumber,
                'other1' => $this->streetOther1,
                'other2' => $this->streetOther2,
            ],
            'administrativeArea1' => $this->administrativeArea1,
            'administrativeArea2' => $this->administrativeArea2,
            'postalCode'          => $this->postalCode,
            'coordinates'         => [
                'latitude'  => $this->coordinates()->latitude(),
                'longitude' => $this->coordinates()->longitude(),
            ],
        ];
    }
}