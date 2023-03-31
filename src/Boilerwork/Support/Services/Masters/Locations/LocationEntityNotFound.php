#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Locations;

final readonly class LocationEntityNotFound extends LocationEntity
{
    public function __construct()
    {
    }

    public function toArray(): array
    {
        return [];
    }
}
