#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

abstract class AggregateRoot
{
    private int $version = 0;

    final public function getAggregateId(): string
    {
        return $this->aggregateId->toPrimitive();
    }

    final public function currentVersion(): int
    {
        return $this->version;
    }

    final protected function increaseVersion(): void
    {
        $this->version = ++$this->currentVersion();
    }
}
