#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Clients;

use Generator;
use Swoole\Coroutine\Http\Client;

/**
 * @desc Connect to a websocket server, receive and send data if proceedes
 *
 * @example
    // Init Connection:
    $wsClient = new WebSocketClient(host: 'stream.exampleserver.com', path: '/stream');
    // Send example data to Websocket server
    $wsClient->push(json_encode([
            'method' => 'SUBSCRIBE',
            'params' => ['param1'],
            'id' => 1,
        ]));
    // Receive data continuously: (a Swoole\WebSocket\Frame is received)
    foreach ($wsClient->receive() as $message) {
        var_dump($message);
    }
 */
class WebSocketClient
{
    public readonly Client $client;

    public function __construct(string $host, string $path = '', int $port = 9443, bool $ssl = true)
    {
        $this->client = new Client(host: $host, port: $port, ssl: $ssl);
        $this->client->upgrade(path: $path);
    }

    public function push(mixed $data, mixed $opcode = WEBSOCKET_OPCODE_TEXT, mixed $flags = null): mixed
    {
        return $this->client->push(data: $data, opcode: $opcode, flags: $flags);
    }

    /**
     * @param float    $timeout     In seconds, the timeout of the request, 1.5 means 1.5 seconds.
     * if operation fails or Generator of Swoole\WebSocket\Frame items if succeeds
     *
     * @see https://openswoole.com/docs/modules/swoole-coroutine-http-client-recv
     */
    public function receive(float $timeout = 0): Generator
    {
        try {
            while ($frame = $this->client->recv(timeout: $timeout)) {
                if ($frame == false) {
                    logger("WS Error : {$this->getErrMsg()}");
                    break;
                } elseif ($frame == '') {
                    logger("Disconnect from WS");
                    $this->close();
                    break;
                }

                yield $frame;
            }
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->close();
        }
    }

    public function close(): mixed
    {
        return $this->client->close();
    }

    public function getErrMsg(): mixed
    {
        return $this->client->errMsg;
    }

    public function getErrCode(): mixed
    {
        return $this->client->errCode;
    }

    public function ping(): mixed
    {
        return $this->client->push('', WEBSOCKET_OPCODE_PING);
    }

    public function pong(): mixed
    {
        return $this->client->push('', WEBSOCKET_OPCODE_PONG);
    }
}
