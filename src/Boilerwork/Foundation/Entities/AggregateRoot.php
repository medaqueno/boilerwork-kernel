#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Entities;

use Boilerwork\Events\AbstractEvent;
use Boilerwork\Messaging\EventPublisher;

abstract class AggregateRoot
{
    use ApplyEvent;

    private array $latestRecordedEvents = [];

    private int $version = 0;

    /**
     * Retrieve Aggregate ID
     */
    final public function aggregateId(): string
    {
        return $this->aggregateId->toPrimitive();
    }

    /**
     * @internal
     * Retrieve Aggregate current version
     */
    final public function currentVersion(): int
    {
        return $this->version;
    }

    /**
     * @internal
     */
    final protected function increaseVersion(): void
    {
        $version = $this->currentVersion();
        $this->version = ++$version;
    }

    /**
     * @internal
     * Retrieve currently recorded events in aggregate.
     */
    final public function recordedEvents(): array
    {
        return $this->latestRecordedEvents;
    }

    /**
     * @internal
     * Clear all recorded events in the aggregate
     */
    final public function clearRecordedEvents(): void
    {
        $this->latestRecordedEvents = [];
    }

    /**
     * Apply event to aggregate and raise it to EventPublisher
     */
    final public function raise(AbstractEvent $event): void
    {
        $this->increaseVersion();

        $this->latestRecordedEvents[] = $event;
        $this->apply($event);

        EventPublisher::getInstance()->raise(event: $event);
    }
}
