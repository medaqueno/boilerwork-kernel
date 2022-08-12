#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Messaging;

use Boilerwork\Application\CommandBus;
use Boilerwork\Infra\Messaging\Message;

abstract class AbstractMessageSubscriberPort
{
    abstract public function __invoke(Message $message): void;

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
