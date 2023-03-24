#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Bus;

use Boilerwork\Foundation\Commands\CommandInterface;

use Boilerwork\Http\Response;

use function call_user_func;
use function container;
use function error;
use function get_class;
use function printf;
use function sprintf;

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
        try {
            // With DI
            $commandHandler = container()->get(get_class($command) . 'Handler');
            // Without DI, should add ..$args
            // $class = get_class($command) . 'Handler';
            // $commandHandler = new $class;

            call_user_func([$commandHandler, 'handle'], $command);
        } catch (\Throwable $th) {
            error($th);

            echo sprintf("\n ERROR HANDLED IN COMMAND BUS:: %s \n", $th->getMessage() ?: "No error message found");
        }
    }
}
