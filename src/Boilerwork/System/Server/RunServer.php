#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Server;

final class RunServer
{
    public function __construct(
        private $serverType = \Swoole\Server::class,
        private array $config = [],
        private array $processes = [],
    ) {

        $this->server = new $serverType($_ENV['SERVER_IP'], intval($_ENV['SERVER_PORT']));

        // https://openswoole.com/docs/modules/swoole-server/configuration
        $this->server->set($config);

        // Set server event handlers

        $this->server->on(
            "start",
            [$this, 'onStart']
        );

        $handleTcpUdp = new HandleTcpUdp();
        $this->server->on(
            "receive",
            [$handleTcpUdp, 'onReceive']
        );

        $handleWorkers = new HandleWorkers();
        $this->server->on(
            "WorkerStart",
            [$handleWorkers, 'onWorkerStart']
        );
        $this->server->on(
            "WorkerStop",
            [$handleWorkers, 'onWorkerStop']
        );

        $this->server->on(
            "WorkerError",
            [$handleWorkers, 'onWorkerError']
        );

        $handleTasks = new HandleTasks();
        $this->server->on(
            "task",
            [$handleTasks, 'onTask']
        );

        // Websocket server may receive requests too, we leave option to
        // comment out and allow it (take care of control the right requests to each server)
        $handleWebSocket = null;
        /*        if ($this->server instanceof \Swoole\WebSocket\Server) {
            $handleWebSocket = new HandleWebSocket();
            $this->server->on(
                "Open",
                [$handleWebSocket, 'onOpen']
            );

            $this->server->on(
                "Message",
                [$handleWebSocket, 'onMessage']
            );

            $this->server->on(
                "Close",
                [$handleWebSocket, 'onClose']
            );
        } */

        if ($this->server instanceof \Swoole\Http\Server) {
            $handleHttp = new HandleHttp(BASE_PATH . '/routes/httpApi.php');
            $this->server->on(
                "request",
                function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($handleHttp, $handleWebSocket) {
                    $handleHttp->onRequest(request: $request, response: $response);

                    /* // We may allow to send requests to Websocket server if it is needed
                    if ($this->server instanceof \Swoole\WebSocket\Server) {
                        $handleWebSocket->onRequest(request: $request, response: $response);
                    }*/
                }
            );
        }
        // var_dump($processes);
        // Add dedicated processes to Server Event Loop
        if (count($processes) > 0) {
            foreach ($processes as $process) {
                $this->server->addProcess($process->process());
            }
        }

        getMemoryStatus();
        // var_dump($this->server);
        $this->server->start();
    }

    public function onStart(\Swoole\Server $server): void
    {
        echo "\nSERVER STARTED: " . get_class($server) . "\n";
        swoole_set_process_name('swoole_server');
    }

    public function getServer(): mixed
    {
        return $this->server;
    }
}
