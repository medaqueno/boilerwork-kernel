#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence\Adapters\PostgreSQL;

use Boilerwork\Domain\AggregateHistory;
use Boilerwork\Domain\EventStore;
use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLWritesClient;

abstract class PostgreSQLEventStoreAdapter implements EventStore
{
    public function __construct(
        private readonly PostgreSQLWritesClient $client
    ) {
    }

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
        $aggregateId = $aggregate->aggregateId();
        $events = $aggregate->recordedEvents();

        if (count($events) === 0) {
            throw new \Boilerwork\Infra\Persistence\Exceptions\PersistenceException(sprintf("No events found in aggregate %s. Nothing will be persisted.", $aggregateId), 409);
        }

        $this->client->getConnection();
        $this->client->initTransaction();

        $currentPersistedAggregate =  $this->client->fetchOne('SELECT "version" FROM "aggregates" WHERE "aggregate_id" = $1', [$aggregateId]);

        if (!$currentPersistedAggregate) {
            $version = 0;

            $this->client->run(
                'INSERT INTO "aggregates" ("aggregate_id", "type", "version") VALUES($1, $2, $3)',
                [
                    $aggregateId,
                    get_class($aggregate),
                    $version // Will be updated after persisting events
                ]
            );
        } else {
            $version = $currentPersistedAggregate['version'];
        }

        if ($version + count($events) !== $aggregate->currentVersion()) {
            throw new \Boilerwork\Infra\Persistence\Exceptions\PersistenceException(sprintf("Expected version and aggregate version must be the same. Aggregate %s history may be corrupted.", $aggregateId), 409);
        }

        foreach ($events as $event) {
            $this->client->run(
                'INSERT INTO "events" ("aggregate_id", "aggregate_type", "data", "version") VALUES($1, $2, $3, $4)',
                [
                    $event->aggregateId(),
                    get_class($aggregate),
                    json_encode($event->serialize()),
                    ++$version
                ]
            );
        }

        $this->client->run(
            'UPDATE "aggregates" SET "version" = $1 WHERE "aggregate_id" = $2',
            [
                $aggregate->currentVersion(),
                $aggregateId
            ],
        );

        $this->client->endTransaction();
        $this->client->putConnection();
    }

    /**
     *  @inheritDoc
     **/
    public function reconstituteHistoryFor(Identity $aggregateId): IsEventSourced
    {
        $this->client->getConnection();

        $eventStream  = $this->client->fetchAll('SELECT "data", "aggregate_type" FROM "events" WHERE "aggregate_id" = $1 ORDER BY "version"', [$aggregateId->toPrimitive()]);
        $this->client->putConnection();

        if (count($eventStream) === 0) {
            throw new \Exception(sprintf('No aggregate has been found with aggregateId: %s', $aggregateId->toPrimitive()), 404);
        }

        $aggregateType = $eventStream[0]['aggregate_type'];

        return $aggregateType::reconstituteFrom(
            new AggregateHistory(
                $aggregateId,
                // Only extract Data column specific to a Domain Event
                array_map(function (array $event) {
                    return json_decode($event['data'], true);
                }, $eventStream)
            )
        );
    }
}
