#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

/**
 *
 **/
final class HandleWebSocket
{
    public function onOpen(\OpenSwoole\WebSocket\Server $server, \OpenSwoole\Http\Request $request): void
    {
        echo "server: handshake success with fd{$request->fd}\n";
    }

    public function onMessage(\OpenSwoole\WebSocket\Server $server, \OpenSwoole\WebSocket\Frame $frame): void
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";

        $server->push($frame->fd, "this is server");
    }

    public function onClose(\OpenSwoole\WebSocket\Server $server, $fd): void
    {
        echo "client {$fd} closed\n";
    }

    /*
    public function onRequest(\OpenSwoole\Http\Request $request, \OpenSwoole\Http\Response $response): void
    {
        echo "REQUEST in Websocket Handler\n\n";
    }
    */
}
