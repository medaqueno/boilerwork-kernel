#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Countries;

final readonly class CountryDtoNotFound extends CountryDto
{
    public function __construct()
    {
    }

    public function toArray(): array
    {
        return [];
    }
}
