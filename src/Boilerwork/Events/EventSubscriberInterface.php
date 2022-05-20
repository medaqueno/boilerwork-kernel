#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Events;

use Boilerwork\Domain\DomainEvent;

interface EventSubscriberInterface
{
    /**
     * Perform Actions with Received Event
     *
     * @param DomainEvent $event
     * @return void
     */
    public function handle(DomainEvent $event): void;

    /** @return string Fully Qualified Class Name */
    public function isSubscribedTo(): string;
}
