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

    public function toArray(?string $lang = null): array
    {
        return [
            'id' => $this->id->toString(),
            ...$this->country->toArray($lang)
        ];
    }
}