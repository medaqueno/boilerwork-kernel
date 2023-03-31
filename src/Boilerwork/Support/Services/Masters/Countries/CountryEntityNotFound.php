#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Countries;

final readonly class CountryEntityNotFound extends CountryEntity
{
    public function __construct()
    {
    }

    public function toArray(): array
    {
        return [];
    }
}
