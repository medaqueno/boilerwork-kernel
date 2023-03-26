#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Airports;

interface AirportsInterface
{
    public function getAirportByIATA(string $iata): AirportDto|AirportDtoNotFound;
}
