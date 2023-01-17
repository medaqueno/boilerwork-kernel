#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

use Boilerwork\Authorization\AuthorizationsMiddleware;
use Boilerwork\Http\RouterMiddleware;
use OpenSwoole\Core\Psr\Middleware\StackHandler;
use OpenSwoole\Http\Server;
use OpenSwoole\Util;

final class HttpServer
{
    private Server $server;

    private StackHandler $stack;

    public function __construct(
        private array $config = [],
        private array $processes = [],
    ) {
        $this->server = new Server(env('SERVER_IP'), intval(env('SERVER_PORT')), \OpenSwoole\Server::POOL_MODE);

        // Overwrite with provided config in param
        $config = array_merge([
            'worker_num' => Util::getCPUNum() * 2,
            'task_worker_num' => Util::getCPUNum(),
            // 'max_conn' => CONFIGURE IF NEEDED AS DOCS RECOMMENDS,
            'debug_mode' => false,
            'log_level' => 0,
            'log_file' => base_path('/logs/swoole_http_server.log'),
            'log_rotation' => \OpenSwoole\Constant::LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%dT%H:%M:%S%z',
        ], $config);

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
            // echo "workerStart\n";
            // var_dump($serv);
        });

        $routesPath = base_path('/routes/httpApi.php');
        $routes = include($routesPath);

        // Add Middlewares here
        $this->stack = (new StackHandler())
            ->add(new RouterMiddleware($routes))
            ->add(new AuthorizationsMiddleware($routes));

        // Add dedicated processes to Server Event Loop
        if (count($processes) > 0) {
            foreach ($processes as $process) {
                $this->server->addProcess($process->process());
            }
        }

        $this->server->setHandler($this->stack);
    }

    public function start(): void
    {
        $this->server->start();
    }

    public function onStart(\OpenSwoole\Server $server): void
    {
        Util::setProcessName('openswoole_server');
        echo PHP_EOL . PHP_EOL;
        echo sprintf("SERVER STARTED: %s v%s at %s:%s", get_class($server), Util::getVersion(),  env('SERVER_IP'), env('SERVER_PORT'));
        echo PHP_EOL . PHP_EOL;
    }
}
