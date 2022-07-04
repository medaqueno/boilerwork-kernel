#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\UI;

use Boilerwork\Application\CommandBus;
use Boilerwork\System\Message\Message;

abstract class AbstractMessagePort
{
    abstract public function __invoke(Message $message): void;

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
