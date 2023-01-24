#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Jobs;

use Boilerwork\Bus\CommandBus;


abstract class AbstractJob
{
    protected string $jobName = __CLASS__;

    abstract public function handle(): void;

    public function __construct()
    {
    }

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
