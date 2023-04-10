#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Airports;

use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Support\ValueObjects\Identity;

readonly class AirportDto
{
    public function __construct(
        public Identity $id,
        public string $iata,
        public MultiLingualText $nameTranslations,
        public Identity $locationId,
        public MultiLingualText $locationNameTranslations,
        public Iso31661Alpha2 $isoAlpha2,
        public MultiLingualText $countryNameTranslations,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'iata' => $this->iata,
            'name' => $this->nameTranslations->toArray(),
            'locationId' => $this->locationId->toString(),
            'locationName' => $this->locationNameTranslations->toArray(),
            'isoAlpha2' => $this->isoAlpha2->toString(),
            'countryName' => $this->countryNameTranslations->toArray(),
        ];
    }
}
