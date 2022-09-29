#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Collection\Behaviours;

use Boilerwork\Support\Collection\Collection;
use function Boilerwork\System\Collection\Behaviours\mb_convert_case;
use function Boilerwork\System\Collection\Behaviours\mb_strtolower;
use function Boilerwork\System\Collection\Behaviours\mb_strtoupper;

trait Strings
{
    /**
     * Trims all values using trim(), returning a new Collection
     *
     * @return Collection|static
     */
    public function trim(): Collection|static
    {
        return $this->map('trim');
    }

    /**
     * Returns a new collection will all values mapped to UPPER case
     *
     * @return Collection|static
     */
    public function upper(): Collection|static
    {
        return $this->map(fn ($item) => function_exists('mb_strtoupper') ? mb_strtoupper($item) : strtoupper($item));
    }

    /**
     * Returns a new collection will all values mapped to lower case
     *
     * @return Collection|static
     */
    public function lower(): Collection|static
    {
        return $this->map(fn ($item) => function_exists('mb_strtolower') ? mb_strtolower($item) : strtolower($item));
    }

    /**
     * Returns a new collection will all string values capitalized
     *
     * @return Collection|static
     */
    public function capitalize(): Collection|static
    {
        return $this->map(fn ($item) => function_exists('mb_convert_case') ? mb_convert_case($item, MB_CASE_TITLE) : ucwords($item));
    }
}
