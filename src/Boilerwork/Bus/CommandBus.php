#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Bus;

use Boilerwork\Foundation\Commands\CommandInterface;

use Boilerwork\Server\ExceptionHandler;
use OpenSwoole\Coroutine;

use function call_user_func;
use function container;
use function error;
use function get_class;
use function sprintf;

final class CommandBus
{
    public function __construct(
        private readonly ExceptionHandler $exceptionHandler,
    ) {

    }
    /**
     * Dispatch the command synchronously
     */
    public function syncHandle(CommandInterface $command)
    {
        $commandHandler = container()->get(get_class($command) . 'Handler');
        // Execute commandHandler
        call_user_func([$commandHandler, 'handle'], $command);
    }

    /**
     * Dispatch the command asynchronously
     */
    public function handle(CommandInterface $command): void
    {
        Coroutine::create(function () use ($command) {
            go(function () use ($command) {
                Coroutine::sleep(1);
                $this->executeHandler($command);
            });
            // Coroutine::sleep(1);
            // echo "co[1] end\n";
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
        } catch (\Exception $th) {
            $this->exceptionHandler->handle($th);
        }
    }
}