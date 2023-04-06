#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Locations;

use Boilerwork\Support\ValueObjects\Geo\Location;
use Boilerwork\Support\ValueObjects\Identity;

readonly class LocationEntity
{
    public function __construct(
        public Identity $id,
        public Location $location
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->location->name(),
            'iso31661Alpha2' => $this->location->iso31661Alpha2()->toString(),
            'coordinates' => $this->location->coordinates()?->toArray(),
        ];
    }
}
