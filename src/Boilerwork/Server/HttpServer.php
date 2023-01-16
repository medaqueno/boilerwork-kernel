#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

use Boilerwork\Http\RouterMiddleware;
use OpenSwoole\Core\Psr\Middleware\StackHandler;
use OpenSwoole\Http\Server;


final class HttpServer
{
    private Server $server;

    private StackHandler $stack;

    public function __construct(
        private array $config = [],
        private array $processes = [],
    ) {
        $this->server = new Server(env('SERVER_IP'), intval(env('SERVER_PORT')));

        $this->server->set($config);

        // Set server event handlers
        $this->server->on(
            "start",
            [$this, 'onStart']
        );

        $this->server->on('finish', function () {
            echo 'task finish';
        });

        $this->server->on('task', function () {
            echo "async task\n";
        });

        $this->server->on('workerStart', function ($serv, $id) {
            echo "workerStart\n";
            // var_dump($serv);
        });

        // Add Middlewares here
        $this->stack = (new StackHandler())
            ->add(new RouterMiddleware());

        $this->server->setHandler($this->stack);
    }

    public function start(): void
    {
        $this->server->start();
    }

    public function onStart(\OpenSwoole\Server $server): void
    {
        echo "\nSERVER STARTED: " . get_class($server) . " at " . env('SERVER_IP') . ":" .  intval(env('SERVER_PORT')) . "\n";
    }
}
