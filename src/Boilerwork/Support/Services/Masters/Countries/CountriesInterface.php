#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Countries;

interface CountriesInterface
{
    public function getCountryById(string $id): CountryDto|CountryDtoNotFound;
}