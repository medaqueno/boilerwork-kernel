#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Locations;

use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Identity;

readonly class LocationReadModel
{
    public function __construct(
        public Identity $id,
        //        public Country $isoAlpha2,
        public string $isoAlpha2,
        public string $locationEs,
        public string $locationEn,
        public Coordinates $coordinates,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'isoAlpha2' => $this->isoAlpha2,
            'name' => $this->id,
            'coordinates' => [
                'latitude' => $this->coordinates->latitude(),
                'longitude' => $this->coordinates->longitude(),
            ],
        ];
    }
}
