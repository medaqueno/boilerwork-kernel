#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\Adapters\InMemory;

use Boilerwork\Domain\AggregateHistory;
use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Infra\Persistence\EventStore;

abstract class InMemoryEventStoreAdapter implements EventStore
{
    /**
     *  Store events in memory
     **/
    private array $memory = [
        'aggregates' => [],
        'events' => [],
    ];

    /**
     * {@inheritDoc}
     *
     * @internal
     * 1. First checks to see if an aggregate exists with the unique identifier it is to use, if there is not one it will create it and consider the current version to be zero.
     * 2. It will then attempt to do an optimistic concurrency test on the data coming in if the expected version does not match the actual version it will raise a concurrency exception.
     * 3. Providing the versions are the same, it will then loop through the events being saved and insert them into the events table, incrementing the version number by one for each event.
     * 4. Finally it will update the Aggregates table to the new current version number for the aggregate. It is important to note that these operations are in a transaction as it is required to insure that optimistic concurrency amongst other things works in a distributed environment.
     *
     * Pseudo code:
     *
        Begin
            version = SELECT version from aggregates where AggregateId = ‘’
            if version is null
                Insert into aggregates
                version = 0
            end
            if expectedversion != version
                raise concurrency problem
            foreach event
                insert event with incremented version number
            update aggregate with last version number
        End Transaction
     *
     * extracted from CQRS Documents by Greg Young - https://cqrs.wordpress.com/documents/building-event-storage/
     **/
    public function append(IsEventSourced $aggregate): void
    {
        $aggregateId = $aggregate->getAggregateId();
        $events = $aggregate->getRecordedEvents();

        if (count($events) === 0) {
            throw new \Boilerwork\Infra\Persistence\Exceptions\PersistenceException(sprintf("No events found in aggregate %s. Nothing will be persisted.", $aggregateId), 409);
        }

        // Retrieve events by aggregateId. Same as select <fields> where aggregateId = <aggregateId>;
        $currentPersistedAggregate = array_filter(
            $this->memory['aggregates'],
            function ($event) use ($aggregateId) {
                return $event[0] === $aggregateId;
            }
        );

        if (!$currentPersistedAggregate) {
            $version = 0;

            $this->memory['aggregates'][] = [
                $aggregateId,
                get_class($aggregate),
                $version,
            ];
        } else {
            $version = $currentPersistedAggregate[0][2];
        }

        if ($version + count($events) !== $aggregate->currentVersion()) {
            throw new \Boilerwork\Infra\Persistence\Exceptions\PersistenceException(sprintf("Expected version and aggregate version must be the same. Aggregate %s history may be corrupted.", $aggregateId), 409);
        }

        foreach ($events as $event) {
            $this->memory['events'][] = [
                $event->getAggregateId(),
                get_class($aggregate),
                json_encode($event->serialize()),
                ++$version
            ];
        }

        foreach ($this->memory['aggregates'] as $key => $value) {
            if ($value[0] === $aggregateId) {
                $this->memory['aggregates'][$key][2] = $aggregate->currentVersion();
                break;
            }
        }
    }

    /**
     *  @inheritDoc
     **/
    public function reconstituteHistoryFor(Identity $aggregateId): IsEventSourced
    {
        $aggregateIdParsed =  $aggregateId->toPrimitive();
        // Filter events by aggregateID And map them to be reconstituted
        $eventStream = array_filter( // Retrieve events by aggregateId. Same as select <fields> where aggregateId = <aggregateId>;
            $this->memory['events'],
            function (array $event) use ($aggregateIdParsed) {
                return $event[0] === $aggregateIdParsed;
            }
        );

        if (count($eventStream) === 0) {
            throw new \Exception(sprintf('No aggregate has been found with aggregateId: %s', $aggregateIdParsed), 404);
        }

        $aggregateType = $eventStream[0][1];

        return $aggregateType::reconstituteFrom(
            new AggregateHistory(
                $aggregateId,
                // Only extract Data column specific to a Domain Event
                array_map(function (array $event) {
                    return json_decode($event[2], true);
                }, $eventStream)
            )
        );
    }
}
