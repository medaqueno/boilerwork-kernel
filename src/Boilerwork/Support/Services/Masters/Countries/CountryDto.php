#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Countries;

use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Support\ValueObjects\Identity;

readonly class CountryDto
{
    public function __construct(
        public string $id,
        public MultiLingualText $nameTranslations,
        public Iso31661Alpha2 $isoAlpha2,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->nameTranslations->translationsToArray(),
            'isoAlpha2' => $this->isoAlpha2->toString(),
        ];
    }
}