#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Entities;

abstract class AggregateRoot
{
    /**
     * Retrieve Entity ID
     */
    final public function id(): string
    {
        return $this->id->toPrimitive();
    }
}
