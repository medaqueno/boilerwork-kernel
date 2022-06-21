#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

use Boilerwork\Domain\DomainEvent;

trait ApplyEvent
{
    /**
     * Execute apply<eventClassName> methods automatically
     **/
    final protected function apply(DomainEvent $event)
    {
        $method = 'apply' .  $this->getName($event::class);
        $this->$method($event);
    }

    /**
     * Extract Class name without namespace
     **/
    final private function getName(string $event): string
    {
        if ($pos = strrpos($event, '\\')) {
            $eventName = substr($event, $pos + 1);
        } else {
            $eventName = $event;
        }

        return $eventName;
    }
}
