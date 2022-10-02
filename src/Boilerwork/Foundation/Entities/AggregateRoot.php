#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Entities;

abstract class AggregateRoot
{
    /**
     * Retrieve Aggregate ID
     */
    final public function aggregateId(): string
    {
        return $this->aggregateId->toPrimitive();
    }
}
