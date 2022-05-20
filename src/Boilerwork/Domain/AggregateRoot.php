#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Events\EventPublisher;

abstract class AggregateRoot implements TracksEvents, IsEventSourced
{
    use ApplyEvent;

    protected readonly Identity $aggregateId;

    protected int $version = 0;

    private array $latestRecordedEvents = [];

    /*
     * TODO: Put separated as it is an interface implementation...
      Maybe this one should be only in this abstract class. Not need to be implemented
    */
    final public function getAggregateId(): string
    {
        return $this->aggregateId->toPrimitive();
    }

    /*
     * TODO: Put separated as it is an interface implementation
    */
    final public function getRecordedEvents(): array
    {
        return $this->latestRecordedEvents;
    }

    /*
     * TODO: Put separated as it is an interface implementation
    */
    final public function clearRecordedEvents(): void
    {
        $this->latestRecordedEvents = [];
    }

    /*
     * TODO: Put separated as it is an interface implementation
    */
    final public function currentVersion(): int
    {
        return $this->version;
    }

    final protected function increaseVersion(): void
    {
        $version = $this->currentVersion();
        $this->version = ++$version;
    }

    protected function raise(DomainEvent $event): void
    {
        $this->increaseVersion();

        $this->latestRecordedEvents[] = $event;
        $this->apply($event);

        EventPublisher::getInstance()->raise(event: $event);
    }

    /*
     * TODO: Put separated as it is an interface implementation
    */
    /**
     * Apply DomainEvents to Aggregate to reconstitute its current state
     **/
    public static function reconstituteFrom(AggregateHistory $aggregateHistory): TracksEvents
    {
        $aggregate = new static(
            aggregateId: $aggregateHistory->getAggregateId()
        );

        foreach ($aggregateHistory->getAggregateHistory() as $event) {
            $aggregate->increaseVersion();
            $aggregate->version = $aggregate->currentVersion();

            $aggregate->apply($event);
        }

        return $aggregate;
    }
}
