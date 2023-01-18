#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Container;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Bind
{
    public function __construct(string $abstract, string $concrete)
    {
        globalContainer()->singletonIf($abstract, $concrete);
    }
}
