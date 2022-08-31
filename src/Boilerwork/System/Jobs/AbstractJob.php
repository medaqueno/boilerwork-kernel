#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Jobs;

use Boilerwork\Application\CommandBus;

abstract class AbstractJob
{
    abstract public function handle(): void;

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
