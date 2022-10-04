#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Entities;

use Boilerwork\Events\AbstractEvent;

trait CanRecordEvents
{
    private array $latestRecordedEvents = [];

    /**
     * 
     * Retrieve currently recorded events in aggregate.
     */
    final public function recordedEvents(): array
    {
        return $this->latestRecordedEvents;
    }

    /**
     * 
     * Clear all recorded events in the aggregate
     */
    final public function clearRecordedEvents(): void
    {
        $this->latestRecordedEvents = [];
    }

    /**
     * Apply event to aggregate and raise it to MessagePublisher
     */
    final public function raise(AbstractEvent $event): void
    {
        $this->increaseVersion();

        $this->latestRecordedEvents[] = $event;
        $this->apply($event);

        eventsPublisher()->raise(event: $event);
    }

    /**
     * Execute apply<eventClassName> methods automatically
     **/
    private function apply(AbstractEvent $event)
    {
        $method = 'apply' .  $this->className($event::class);
        $this->$method($event);
    }

    /**
     * Extract Class name without namespace
     **/
    private function className(string $event): string
    {
        if ($pos = strrpos($event, '\\')) {
            $eventName = substr($event, $pos + 1);
        } else {
            $eventName = $event;
        }

        return $eventName;
    }

    private int $version = 0;

    /**
     * 
     * Retrieve Aggregate current version
     */
    final public function currentVersion(): int
    {
        return $this->version;
    }

    /**
     * 
     */
    private function increaseVersion(): void
    {
        $version = $this->currentVersion();
        $this->version = ++$version;
    }
}
