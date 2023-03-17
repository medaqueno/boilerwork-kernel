#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Locations;

use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Support\ValueObjects\Identity;

readonly class LocationDto
{
    public function __construct(
        public Identity $id,
        public Iso31661Alpha2 $isoAlpha2,
        public MultiLingualText $nameTranslations,
        public Coordinates $coordinates,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'isoAlpha2' => $this->isoAlpha2->toString(),
            'name' => $this->nameTranslations->translationsToArray(),
            'coordinates' => [
                'latitude' => $this->coordinates->latitude(),
                'longitude' => $this->coordinates->longitude(),
            ],
        ];
    }
}
