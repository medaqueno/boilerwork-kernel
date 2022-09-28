#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Adapters;

use Boilerwork\Domain\AggregateHistory;
use Boilerwork\Domain\EventStore;
use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Persistence\Repositories\SqlWritesRepository;

abstract class PostgreSQLEventStoreAdapter implements EventStore
{
    public function __construct(
        private readonly SqlWritesRepository $writesRepository
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

        $this->writesRepository->initTransaction();

        $query = $this->writesRepository
            ->select(['version'])
            ->from("aggregates")
            ->where("aggregate_id = :where_aggregate_id")
            ->bindValues([
                ':where_aggregate_id' => $aggregateId
            ]);

        $currentPersistedAggregate = $this->writesRepository->fetchOne($query->getStatement(), $query->getBindValues());

        if (!$currentPersistedAggregate) {
            $version = 0;

            $query = $this->writesRepository->insert([
                "aggregate_id",
                "type",
                "version",
            ])
                ->into("aggregates")
                ->bindValues([
                    ':aggregate_id' => $aggregateId,
                    ':type' => get_class($aggregate),
                    ':version' => $version // Will be updated after persisting events
                ]);

            $this->writesRepository->execute($query->getStatement(), $query->getBindValues());
        } else {
            $version = $currentPersistedAggregate['version'];
        }

        if ($version + count($events) !== $aggregate->currentVersion()) {
            throw new \Boilerwork\Infra\Persistence\Exceptions\PersistenceException(sprintf("Expected version and aggregate version must be the same. Aggregate %s history may be corrupted.", $aggregateId), 409);
        }

        foreach ($events as $event) {
            $query = $this->writesRepository->insert([
                "aggregate_id",
                "aggregate_type",
                "data",
                "version",
            ])
                ->into("events")
                ->bindValues([
                    ':aggregate_id' => $event->aggregateId(),
                    ':aggregate_type' => get_class($aggregate),
                    ':data' => json_encode($event->serialize()),
                    ':version' => ++$version
                ]);

            $this->writesRepository->execute($query->getStatement(), $query->getBindValues());
        }

        $query = $this->writesRepository->update([
            "version",
        ])
            ->table("aggregates")
            ->where("aggregate_id = :where_aggregate_id")
            ->bindValues([
                ':where_aggregate_id' => $aggregateId,
                ':version' => $aggregate->currentVersion()
            ]);

        $this->writesRepository->execute($query->getStatement(), $query->getBindValues());

        $this->writesRepository->endTransaction();
    }

    /**
     *  @inheritDoc
     **/
    public function reconstituteHistoryFor(Identity $aggregateId): IsEventSourced
    {
        $query = $this->writesRepository
            ->select(['data', 'aggregate_type'])
            ->from("events")
            ->where("aggregate_id = :where_aggregate_id")
            ->orderBy(['version ASC'])
            ->bindValues([
                ':where_aggregate_id' => $aggregateId->toPrimitive()
            ]);

        $eventStream = $this->writesRepository->fetchAll($query->getStatement(), $query->getBindValues());

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
