#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Commands;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}
