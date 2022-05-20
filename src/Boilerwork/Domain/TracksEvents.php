#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

/**
 * An object that records the events that happened to it since the last time it was cleared, or since it was
 * restored from persistence.
 */
interface TracksEvents
{
    /**
     * Get all the Domain Events that were recorded since the last time it was cleared, or since it was
     * restored from persistence. This does not include events that were recorded prior.
     */
    public function getRecordedEvents(): array;

    /**
     * Clears the record of new Domain Events. This doesn't clear the history of the object.
     */
    public function clearRecordedEvents(): void;

    public function currentVersion(): int;

    public function getAggregateId(): string;
}
