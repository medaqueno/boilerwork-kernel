#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

use Boilerwork\Domain\ValueObjects\Identity;

/**
 * Receive Events from persistence, check events belong to their owner aggregate and convert them to an array of DomainEvents
 */
final class AggregateHistory
{
    private array $history = [];

    public function __construct(
        private Identity $aggregateId,
        private readonly array $events
    ) {
        foreach ($events as $event) {
            $event = $event['type']::unserialize($event);

            if ($event->getAggregateId() !== $aggregateId->toPrimitive()) {
                throw new \Exception('Aggregate history is corrupted');
            }

            $this->history[] = $event;
        }

        $this->aggregateId = $aggregateId;
    }

    public function getAggregateId(): Identity
    {
        return $this->aggregateId;
    }

    public function getAggregateHistory(): array
    {
        return $this->history;
    }
}
