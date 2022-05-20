#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Application;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}
