#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Entities;

use Boilerwork\Events\AbstractEvent;

trait ApplyEvent
{
    /**
     * Execute apply<eventClassName> methods automatically
     **/
    final protected function apply(AbstractEvent $event)
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
}
