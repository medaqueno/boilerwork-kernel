#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

use Boilerwork\Events\EventPublisher;

/**
 * An object that records the events that happened to it since the last time it was cleared, or since it was
 * restored from persistence.
 */
trait TracksEventsTrait
{
    use ApplyEvent;

    private array $latestRecordedEvents = [];

    final public function getRecordedEvents(): array
    {
        return $this->latestRecordedEvents;
    }

    final public function clearRecordedEvents(): void
    {
        $this->latestRecordedEvents = [];
    }

    final public function raise(DomainEvent $event): void
    {
        $this->increaseVersion();

        $this->latestRecordedEvents[] = $event;
        $this->apply($event);

        EventPublisher::getInstance()->raise(event: $event);
    }
}
