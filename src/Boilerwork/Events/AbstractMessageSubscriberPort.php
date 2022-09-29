#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Events;

use Boilerwork\Bus\CommandBus;

abstract class AbstractMessageSubscriberPort
{
    abstract public function __invoke(Message $message): void;

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
