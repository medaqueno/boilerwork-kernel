#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Bus;

use Boilerwork\Foundation\Commands\CommandInterface;

final class CommandBus
{
    /**
     * Dispatch the command synchronously
     */
    public function syncHandle(CommandInterface $command)
    {
        // Execute commandHandler
        $this->executeHandler($command);
    }

    /**
     * Dispatch the command asynchronously
     */
    public function handle(CommandInterface $command): void
    {
        go(function () use ($command) {
            $this->executeHandler($command);
        });
    }

    private function executeHandler(CommandInterface $command): void
    {
        // With DI
        $commandHandler = globalContainer()->get(get_class($command) . 'Handler');
        // Without DI, should add ..$args
        // $class = get_class($command) . 'Handler';
        // $commandHandler = new $class;

        try {
            call_user_func([$commandHandler, 'handle'], $command);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
