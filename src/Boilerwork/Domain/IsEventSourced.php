#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

/**
 * An AggregateRoot, that can be reconstituted from an AggregateHistory.
 */
interface IsEventSourced extends TracksEvents
{
    public function getAggregateId(): string;

    public function currentVersion(): int;

    public static function reconstituteFrom(AggregateHistory $aggregateHistory): TracksEvents;
}
