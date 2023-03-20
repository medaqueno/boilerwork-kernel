<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Validation\Assert;

class Location extends ValueObject
{
    private function __construct(
        private readonly string $name,
        private readonly Coordinates $coordinates,
    ) {
        Assert::lazy()->tryAll()
            ->that($name)
            ->notEmpty('Name must not be empty', 'location.invalidName')
            ->verifyNow();
    }

    public static function fromScalars(
        string $name,
        float $latitude,
        float $longitude,
    ): self {
        return new self(
            name: $name,
            coordinates: Coordinates::fromScalars($latitude, $longitude),
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @return array{
     *     name: string,
     *     coordinates: array{ latitude: float, longitude: float }|null
     * }
     * @see Coordinates::toArray()
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'coordinates' => $this->coordinates->toArray(),
        ];
    }
}
