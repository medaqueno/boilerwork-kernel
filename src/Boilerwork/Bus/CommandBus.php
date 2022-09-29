#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Bus;

use Boilerwork\Foundation\Commands\CommandInterface;

final class CommandBus
{
    // private float $time;

    public function __construct()
    {
        // $this->time = microtime(true);
    }

    public function syncHandle(CommandInterface $command)
    {
        $commandHandler = globalContainer()->get(get_class($command) . 'Handler');

        // Execute commandHandler
        try {
            call_user_func([$commandHandler, 'handle'], $command);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Dispatch the command
     */
    public function handle(CommandInterface $command): void
    {
        go(function () use ($command) {
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
        });
    }

    private function getCommandName(CommandInterface $command): string
    {
        $commandClass = get_class($command);

        if ($pos = strrpos($commandClass, '\\')) {
            $commandName = substr($commandClass, $pos + 1);
        } else {
            $commandName = $commandClass;
        }

        return $commandName;
    }
}
