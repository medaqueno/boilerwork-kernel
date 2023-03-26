#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Airports;

final readonly class AirportDtoNotFound extends AirportDto
{
    public function __construct()
    {
    }

    public function toArray(): array
    {
        return [];
    }
}
