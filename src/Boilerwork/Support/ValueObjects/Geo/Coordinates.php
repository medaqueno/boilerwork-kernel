#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;

final class Coordinates extends ValueObject
{
    public function __construct(
        private float $latitude,
        private float $longitude
    ) {
        Assert::lazy()->tryAll()
            ->that($latitude)
            ->between(-90, 90, 'Value must be greater or equal than -90 or lesser or equal than 90', 'coordinates.invalidLatitude')
            ->that($longitude)
            ->between(-180, 180, 'Value must be greater or equal than -180 or lesser or equal than 180', 'coordinates.invalidLongitude')
            ->verifyNow();
    }

    public static function fromValues(
        float $latitude,
        float $longitude
    ): self {
        return new self(latitude: $latitude, longitude: $longitude);
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function toString(): string
    {
        return $this->latitude . ", " . $this->longitude;
    }

    public function toPrimitive(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [$this->latitude, $this->longitude];
    }
}
