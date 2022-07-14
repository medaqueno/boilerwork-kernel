#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Collection;

use ArrayIterator;

/**
 * @see https://github.com/somnambulist-tech/collection
 */
class Collection extends AbstractCollection
{
    public function __construct(mixed $items = [])
    {
        $this->items = $items;
    }
}
