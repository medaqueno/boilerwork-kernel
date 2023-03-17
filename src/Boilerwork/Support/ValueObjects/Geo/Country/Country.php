<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo\Country;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Validation\Assert;

class Country extends ValueObject
{
    private function __construct(
        private readonly string $name,
        private readonly ?Iso31661Alpha2 $iso31661Alpha2,
        private readonly ?Iso31661Alpha3 $iso31661Alpha3,
        private readonly ?Coordinates $coordinates
    ) {
        Assert::lazy()->tryAll()
            ->that($name)
            ->notEmpty('Name must not be empty', 'country.invalidName')
            ->that($iso31661Alpha2)
            ->satisfy(function () use ($iso31661Alpha2, $iso31661Alpha3) {
                return $iso31661Alpha2 !== null || $iso31661Alpha3 !== null;
            }, 'At least one ISO 31661-X must be provided', 'country.notFoundIso')
            ->verifyNow();
    }

    public static function fromScalars(
        string $name,
        string $iso31661Alpha2,
        string $iso31661Alpha3,
        ?float $latitude,
        ?float $longitude
    ): self {
        return new self(
            name: $name,
            iso31661Alpha2: Iso31661Alpha2::fromString($iso31661Alpha2),
            iso31661Alpha3: Iso31661Alpha3::fromString($iso31661Alpha3),
            coordinates: Coordinates::fromScalars($latitude, $longitude)
        );
    }

    public static function fromScalarsWithIso31661Alpha2(
        string $name,
        string $iso31661Alpha2,
        ?float $latitude,
        ?float $longitude
    ): self {
        return new self(
            name: $name,
            iso31661Alpha2: Iso31661Alpha2::fromString($iso31661Alpha2),
            iso31661Alpha3: null,
            coordinates: Coordinates::fromScalars($latitude, $longitude)
        );
    }

    public static function fromScalarsWithIso31661Alpha3(
        string $name,
        string $iso31661Alpha3,
        ?float $latitude,
        ?float $longitude
    ): self {
        return new self(
            name: $name,
            iso31661Alpha2: null,
            iso31661Alpha3: Iso31661Alpha3::fromString($iso31661Alpha3),
            coordinates: Coordinates::fromScalars($latitude, $longitude)
        );
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function iso31661Alpha2(): ?Iso31661Alpha2
    {
        return $this->iso31661Alpha2;
    }

    public function iso31661Alpha3(): ?Iso31661Alpha3
    {
        return $this->iso31661Alpha3;
    }

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'iso31661Alpha2' => $this->iso31661Alpha2?->toString(),
            'iso31661Alpha3' => $this->iso31661Alpha3?->toString(),
            'coordinates' => $this->coordinates?->toArray(),
        ];
    }
}
