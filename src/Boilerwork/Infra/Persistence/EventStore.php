#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence;

use Boilerwork\Domain\TracksEvents;
use Boilerwork\Domain\ValueObjects\Identity;

interface EventStore
{
    /**
     *  Add Events to Persistence
     *
     **/
    public function append(TracksEvents $events): void;

    /**
     *  Get Event Stream in persistence where id = X
     **/
    public function getAggregateHistoryFor(Identity $id): TracksEvents;
}
