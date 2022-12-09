#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Adapters\Postgres;

use Boilerwork\Foundation\Entities\AggregateHistory;
use Boilerwork\Foundation\Entities\EventStore;
use Boilerwork\Foundation\Entities\IsEventSourced;
use Boilerwork\Persistence\Repositories\WritesRepository;
use Boilerwork\Support\ValueObjects\Identity;

abstract class PostgreSQLEventStoreAdapter implements EventStore
{
    public function __construct(
        private readonly WritesRepository $writesRepository
    ) {
    }

    /**
     * {@inheritDoc}
     *
     *
     * 1. First checks to see if an aggregate exists with the unique identifier it is to use, if there is not one it will create it and consider the current version to be zero.
     * 2. It will then attempt to do an optimistic concurrency test on the data coming in if the expected version does not match the actual version it will raise a concurrency exception.
     * 3. Providing the versions are the same, it will then loop through the events being saved and insert them into the events table, incrementing the version number by one for each event.
     * 4. Finally it will update the Aggregates table to the new current version number for the aggregate. It is important to note that these operations are in a transaction as it is required to insure that optimistic concurrency amongst other things works in a distributed environment.
     *
     * Pseudo code:
     *
        Begin
            version = SELECT version from aggregates where id = ‘’
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
        $id = $aggregate->id();
        $events = $aggregate->recordedEvents();

        if (count($events) === 0) {
            throw new \Boilerwork\Persistence\Exceptions\PersistenceException(sprintf("No events found in aggregate %s. Nothing will be persisted.", $id), 409);
        }

        $this->writesRepository->queryBuilder->initTransaction();

        $currentPersistedAggregate = $this->writesRepository->queryBuilder
            ->select('version')
            ->from('aggregates')
            ->where('aggregate_id = :where_aggregate_id')
            ->setParameters([
                'where_aggregate_id' => $id
            ])->fetchOne();

        if (!$currentPersistedAggregate) {
            $version = 0;

            $this->writesRepository->queryBuilder
                ->insert('aggregates')
                ->values([
                    'aggregate_id' => ':aggregate_id',
                    'type' => ':type',
                    'version' => ':version',
                ])
                ->setParameters([
                    'aggregate_id' => $id,
                    'type' => get_class($aggregate),
                    'version' => $version // Will be updated after persisting events
                ])->execute();
        } else {
            $version = $currentPersistedAggregate['version'];
        }

        if ($version + count($events) !== $aggregate->currentVersion()) {
            throw new \Boilerwork\Persistence\Exceptions\PersistenceException(sprintf("Expected version and aggregate version must be the same. Aggregate %s history may be corrupted.", $id), 409);
        }

        foreach ($events as $event) {
            $this->writesRepository->queryBuilder
                ->insert('events')
                ->values([
                    'aggregate_id' => ':aggregate_id',
                    'aggregate_type' => ':aggregate_type',
                    'data' => ':data',
                    'version' => ':version',
                ])
                ->setParameters([
                    'aggregate_id' => $event->id(),
                    'aggregate_type' => get_class($aggregate),
                    'data' => json_encode($event->serialize()),
                    'version' => ++$version
                ])->execute();
        }

        $this->writesRepository->queryBuilder
            ->update('aggregates')
            ->set('version', ':version')
            ->where("aggregate_id = :where_aggregate_id")
            ->setParameters([
                'where_aggregate_id' => $id,
                'version' => $aggregate->currentVersion()
            ])->execute();

        $this->writesRepository->queryBuilder->endTransaction();
    }

    /**
     *  @inheritDoc
     **/
    public function reconstituteHistoryFor(Identity $id): IsEventSourced
    {
        $eventStream = $this->writesRepository->queryBuilder
            ->select('data', 'aggregate_type')
            ->from("events")
            ->where("aggregate_id = :where_aggregate_id")
            ->orderBy('version', 'ASC')
            ->setParameters([
                'where_aggregate_id' => $id->toPrimitive()
            ])
            ->fetchAllAssociative();

        if (count($eventStream) === 0) {
            throw new \Exception(sprintf('No aggregate has been found with id: %s', $id->toPrimitive()), 404);
        }

        $aggregateType = $eventStream[0]['aggregate_type'];

        return $aggregateType::reconstituteFrom(
            new AggregateHistory(
                $id,
                // Only extract Data column specific to a Domain Event
                array_map(function (array $event) {
                    return json_decode($event['data'], true);
                }, $eventStream)
            )
        );
    }
}
