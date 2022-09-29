#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Entities;

/**
 * An AggregateRoot, that can be reconstituted from an AggregateHistory.
 */
trait IsEventSourcedTrait
{
    /**
     * Apply DomainEvents to Aggregate to reconstitute its current state
     **/
    public static function reconstituteFrom(AggregateHistory $aggregateHistory): IsEventSourced
    {
        $aggregate = new static(
            aggregateId: $aggregateHistory->aggregateId()
        );

        foreach ($aggregateHistory->aggregateHistory() as $event) {
            $aggregate->increaseVersion();
            $aggregate->apply($event);
        }

        return $aggregate;
    }
}
