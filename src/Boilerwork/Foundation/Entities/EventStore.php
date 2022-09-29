#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Entities;

use Boilerwork\Support\ValueObjects\Identity;

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
