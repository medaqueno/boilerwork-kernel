#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Clients;

use Boilerwork\Helpers\Singleton;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Swoole\Coroutine\Channel;

class MessagePool
{
    use Singleton;

    public readonly \Swoole\Coroutine\Channel $upstreamPool;
    public readonly \Swoole\Coroutine\Channel $downstreamPool;

    public int $status = 0;

    private function __construct()
    {
        $host = $_ENV['MESSAGE_HOST'] ?? 'rabbitmq';
        $port = $_ENV['MESSAGE_PORT'] ?? 5672;
        $username = $_ENV['MESSAGE_USERNAME'] ?? 'guest';
        $password = $_ENV['MESSAGE_PASSWORD'] ?? 'guest';

        $size = 1;

        $this->fillPool($host, $port, $username, $password, $size);
    }

    protected function fillPool($host, $port, $username, $password, $size): void
    {
        $size = (int) $size;

        go(function () use ($host, $port, $username, $password, $size) {
            $this->upstreamPool = new Channel($size);
            $this->downstreamPool = new Channel($size);
            try {
                for ($i = 0; $i < $size; $i++) {
                    $res = new \PhpAmqpLib\Connection\AMQPStreamConnection(
                        host: $host,
                        port: $port,
                        user: $username,
                        password: $password,
                    );

                    if ($res === false) {
                        // error('failed to connect RabbitMq server.');
                        echo 'failed to connect RabbitMq server.';
                        throw new \RuntimeException("failed to connect RabbitMq server.");
                    } else {
                        $this->putUpstreamConn($res);
                    }
                }

                for ($i = 0; $i < $size; $i++) {
                    $res = new \PhpAmqpLib\Connection\AMQPStreamConnection(
                        host: $host,
                        port: $port,
                        user: $username,
                        password: $password,
                    );

                    if ($res === false) {
                        // error('failed to connect RabbitMq server.');
                        echo 'failed to connect RabbitMq server.';
                        throw new \RuntimeException("failed to connect RabbitMq server.");
                    } else {
                        $this->putDownStreamConn($res);
                    }
                }

                echo "Message POOL UPSTREAM CREATED. " . $this->upstreamPool->capacity . " connections opened\n";
                echo "Message POOL DOWNSTREAM CREATED. " . $this->downstreamPool->capacity . " connections opened\n";

                if ($this->upstreamPool->capacity === 0  && $this->downstreamPool->capacity === 0) {
                    $this->status = 0;
                } else {
                    $this->status = 1;
                }
            } catch (\Exception $e) {
                // error($e->getMessage());
                $this->status = 0;
                $this->close();
            }
        });
    }

    public function getDownstreamConn(): AMQPStreamConnection|bool
    {
        return $this->downstreamPool->pop();
    }

    public function getUpstreamConn(): AMQPStreamConnection|bool
    {
        return $this->upstreamPool->pop();
    }

    public function putUpstreamConn(AMQPStreamConnection $connection): void
    {
        $this->upstreamPool->push($connection);
    }

    public function putDownStreamConn(AMQPStreamConnection $connection): void
    {
        $this->downstreamPool->push($connection);
    }

    public function close(): void
    {
        $this->downstreamPool->close();
        $this->upstreamPool->close();
        // $this->upstreamPool = $this->downstreamPool = null;
    }
}
