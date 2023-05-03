#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Locations;

use Boilerwork\Support\ValueObjects\Geo\Location;
use Boilerwork\Support\ValueObjects\Identity;

final readonly class LocationEntityNotFound extends LocationEntity
{
    public function __construct()
    {
        $nullIdentity = Identity::create();
        $nullLocation = Location::fromScalars(['ES' => 'null'],'ES',0.00, 0.00);

        parent::__construct($nullIdentity, $nullLocation);
    }
    public function toArray(?string $lang = null): array
    {
        return [];
    }
}

