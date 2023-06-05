#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

use Boilerwork\Authorization\AuthorizationsMiddleware;
use Boilerwork\Server\Middleware\UpkeepMiddleware;
use Boilerwork\Tracking\TrackingMiddleware;
use Boilerwork\Router\Adapter\FastRoute;
use Boilerwork\Server\Events\ServerStartHandler;
use Boilerwork\Server\Events\TaskStartHandler;
use Boilerwork\Server\Events\WorkerErrorHandler;
use Boilerwork\Server\Events\WorkerExitHandler;
use Boilerwork\Server\Events\WorkerStartHandler;
use Boilerwork\Server\Middleware\ErrorHandlerMiddleware;
use Boilerwork\Server\Middleware\RequestMiddleware;
use OpenSwoole\Constant;
use OpenSwoole\Core\Psr\Middleware\StackHandler;
use OpenSwoole\Http\Server as HttpServer;
use OpenSwoole\Server;
use OpenSwoole\Util;

final class Start
{
    private HttpServer $server;
    private StackHandler $stack;

    public function __construct($customConfig = [], array $routes = [])
    {
        $this->server = new HttpServer(
            host: env('SERVER_IP'),
            port: intval(env('SERVER_PORT')),
            mode: Server::POOL_MODE
        );

        $this->loadServerConfig($customConfig);
        $this->loadServerEvents();

        $this->stack = (new StackHandler())
            ->add(RequestMiddleware::getInstance(new FastRoute($routes)))
            ->add(AuthorizationsMiddleware::getInstance($routes))
            ->add(new ErrorHandlerMiddleware())
            ->add(new TrackingMiddleware()); // MUST always be the first to process an incoming request
    }

    public function start(): void
    {
        $this->server->setHandler($this->stack);
        $this->server->start();
    }

    private function loadServerEvents(): void
    {
        $this->server->on('start', new ServerStartHandler());
        $this->server->on('workerStart', new WorkerStartHandler());
        $this->server->on('workerExit', new WorkerExitHandler());
        $this->server->on('workerError', new WorkerErrorHandler());
        $this->server->on('task', new TaskStartHandler());
    }

    private function loadServerConfig(array $customConfig = []): void
    {
        $config = array_merge([
            'worker_num'      => Util::getCPUNum(),
            'task_worker_num' => Util::getCPUNum(),
            'max_request' => 1000,
            // 'max_conn' => CONFIGURE IF NEEDED AS DOCS RECOMMENDS,
            'debug_mode'      => false,
            'log_level'       => 0,
            'log_file'        => base_path('/logs/swoole_http_server.log'),
            'log_rotation'    => Constant::LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%dT%H:%M:%S%z',
        ], $customConfig);

        $this->server->set($config);
    }
}
