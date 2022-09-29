#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

/**
 *
 **/
final class HandleWebSocket
{
    public function onOpen(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request): void
    {
        echo "server: handshake success with fd{$request->fd}\n";
    }

    public function onMessage(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame): void
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";

        $server->push($frame->fd, "this is server");
    }

    public function onClose(\Swoole\WebSocket\Server $server, $fd): void
    {
        echo "client {$fd} closed\n";
    }

    /*
    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response): void
    {
        echo "REQUEST in Websocket Handler\n\n";
    }
    */
}
