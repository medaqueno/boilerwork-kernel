#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Events;

use Kernel\Domain\DomainEvent;

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
