#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Persistence;

use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\ValueObjects\Identity;

interface EventStore
{
    /**
     *  Add Events to Persistence
     *
     **/
    public function append(IsEventSourced $events): void;

    /**
     *  Get Event Stream in persistence where id = X
     **/
    public function reconstituteHistoryFor(Identity $id): IsEventSourced;
}
