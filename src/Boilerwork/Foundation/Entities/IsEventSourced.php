#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Entities;

/**
 * An AggregateRoot, that can be reconstituted from an AggregateHistory.
 */
interface IsEventSourced
{
    public function id(): string;

    public function recordedEvents(): array;

    public function currentVersion(): int;

    public static function reconstituteFrom(AggregateHistory $aggregateHistory): IsEventSourced;
}
