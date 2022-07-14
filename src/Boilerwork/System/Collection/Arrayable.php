#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Collection;

interface Arrayable
{
    public function toArray(): array;
}
