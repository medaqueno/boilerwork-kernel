#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

/**
 * An AggregateRoot, that can be reconstituted from an AggregateHistory.
 */
interface IsEventSourced
{
    public static function reconstituteFrom(AggregateHistory $aggregateHistory): TracksEvents;
}
