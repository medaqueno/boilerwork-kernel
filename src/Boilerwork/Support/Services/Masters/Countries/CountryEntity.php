#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Countries;

use Boilerwork\Support\ValueObjects\Geo\Country\Country;
use Boilerwork\Support\ValueObjects\Identity;

readonly class CountryEntity
{
    public function __construct(
        public Identity $id,
        public Country $country
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->country->name(),
            'iso31661Alpha2' => $this->country->iso31661Alpha2()->toString(),
            'coordinates' => $this->country->coordinates()->toArray(),
        ];
    }
}
