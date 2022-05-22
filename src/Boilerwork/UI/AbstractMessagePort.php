#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\UI;

use Boilerwork\Application\CommandBus;
use PhpAmqpLib\Message\AMQPMessage;

abstract class AbstractMessagePort
{
    abstract public function __invoke(AMQPMessage $msg): void;

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
