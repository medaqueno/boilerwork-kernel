#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Events\EventPublisher;

abstract class AggregateRoot
{
    use ApplyEvent;

    private array $latestRecordedEvents = [];

    private int $version = 0;

    final public function aggregateId(): string
    {
        return $this->aggregateId->toPrimitive();
    }

    final public function currentVersion(): int
    {
        return $this->version;
    }

    final protected function increaseVersion(): void
    {
        $version = $this->currentVersion();
        $this->version = ++$version;
    }

    final public function recordedEvents(): array
    {
        return $this->latestRecordedEvents;
    }

    final public function clearRecordedEvents(): void
    {
        $this->latestRecordedEvents = [];
    }

    final public function raise(AbstractEvent $event): void
    {
        $this->increaseVersion();

        $this->latestRecordedEvents[] = $event;
        $this->apply($event);

        EventPublisher::getInstance()->raise(event: $event);
    }
}
