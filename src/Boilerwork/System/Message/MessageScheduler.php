#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Message;

use Boilerwork\System\Clients\MessagePool;
use Boilerwork\System\IsProcessInterface;
use Swoole\Process;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Swoole\Coroutine;

final class MessageScheduler implements IsProcessInterface
{
    private Process $process;

    public AMQPStreamConnection|bool $connection;

    public function __construct(private $subscriptionProvider)
    {
        $this->process = (new Process(
            function () {
                Coroutine\run(function () {
                    $pool = \Boilerwork\System\Container\Container::getInstance()->get(MessagePool::class);
                    $connection = $pool->getDownstreamConn();

                    Coroutine\System::wait(5);

                    if ($connection === false || $pool->status === 0 || $pool->downstreamPool->capacity < 1) {
                        echo 'Error opening connection to AMQP';
                        // error('Error opening connection to AMQP');

                        return;
                    }
                    $channel = $connection->channel();

                    foreach ($this->subscriptionProvider->getSubscriptions() as $item) {
                        if ($item['exchange'] !== null) {
                            // Use Exchanges
                            //
                            $channel->exchange_declare($item['exchange'], 'fanout', false, false, false);

                            // Automatic Queue creation and queue/exchange binding if don't exist (should we allow devs to do it?)
                            $channel->queue_declare($item['queue'], false, false, true, false);
                            $channel->queue_bind($item['queue'], $item['exchange']);

                            // Subscribes to exchange. But Queue must be created in Rabbit Admin and binded to Exchange manually
                            // We can also create queue automatically if doesn't exist declaring the queue
                            // list($queue_name,,) = $channel->queue_declare("", false, false, true, false);
                            // $channel->queue_bind($queue_name, $item['exchange']);
                        } else {
                            // Direct messages to Queues
                            //
                            // Create Queue if doesn't exist. It may be optional, depending on the messaging strategy selected
                            $channel->queue_declare($item['queue'], false, false, false, false);
                        }

                        // Consume message from queue and callback corresponding class
                        $channel->basic_consume($item['queue'], '', false, true, false, false, function (AMQPMessage $msg) use ($item) {
                            go(function () use ($msg, $item) {
                                $class = \Boilerwork\System\Container\Container::getInstance()->get($item['target']);
                                call_user_func($class, $msg);
                            });
                        });
                    }

                    while ($channel->is_open()) {
                        $channel->wait();
                    }

                    $channel->close();
                    $connection->close();
                });
            }
        ));
    }

    public function process(): Process
    {
        return $this->process;
    }
}
