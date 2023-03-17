#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Locations;

interface LocationsInterface
{
    public function getLocationById(string $id): LocationDto|LocationDtoNotFound;
}
