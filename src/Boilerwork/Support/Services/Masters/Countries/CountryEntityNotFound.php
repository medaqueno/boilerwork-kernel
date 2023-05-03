#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Countries;

use Boilerwork\Support\ValueObjects\Geo\Country\Country;
use Boilerwork\Support\ValueObjects\Identity;

final readonly class CountryEntityNotFound extends CountryEntity
{
    public function __construct()
    {
        $nullIdentity = Identity::create();
        $nullCountry = Country::fromScalarsWithIso31661Alpha2(['ES' => 'null'],'ES',0.00, 0.00);

        parent::__construct($nullIdentity, $nullCountry);
    }
    public function toArray(?string $lang = null): array
    {
        return [];
    }
}
