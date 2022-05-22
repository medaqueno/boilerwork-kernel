#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Clients;

interface MessagingInterface
{
    /**
     * Example Use:
     * $messagingClient = new MessagingClient();
     *
     * // Using Exchange. Queue may be omitted, because queues are binded in Broker Admin manually or by subscribers
     * $messagingClient->publish(message: 'this is an example message', queue: 'test-Message/withExchange', exchange: 'exchangeTest');
     * or better:
     * $messagingClient->publish(message: 'this is an example message', queue: null, exchange: 'exchangeTest');
     *
     * // Using only queues
     * $messagingClient->publish(message: 'this is an example message', queue: 'test-Message/onlyQueue');
     **/
    public function publish(string $message, string $queue): void;

    public function subscribe(string $queue, string $exchange = null, callable $fn): void;
}
