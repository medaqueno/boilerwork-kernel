#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Messaging;

use Boilerwork\System\IsProcessInterface;
use Boilerwork\System\Messaging\MessagingClientInterface;
use Boilerwork\System\Messaging\MessagingProviderInterface;
use Boilerwork\System\Messaging\Message;
use DateTime;
use Exception;
use Swoole\Process;

final class MessagingScheduler implements IsProcessInterface
{
    private Process $process;

    public function __construct(
        private MessagingProviderInterface $subscriptionProvider
    ) {
        $topics = [];
        $messageClient = \Boilerwork\System\Container\Container::getInstance()->get(MessagingClientInterface::class);

        // Safe check: No consumer subscriptions, create empty process that will be attached to Server
        if (count($this->subscriptionProvider->getSubscriptions()) === 0) {
            $this->process = (new Process(function () {
            }));
            return;
        }

        foreach ($this->subscriptionProvider->getSubscriptions() as $item) {
            $topics[] = $item['topic'];
        }

        $this->process = (new Process(
            callback: function () use ($messageClient, $topics) {

                $consumer = $messageClient->subscribe(topics: $topics);

                if ($consumer === null) {
                    echo "\n\n########\nERROR CONNECTING TO KAFKA BROKER\n########\n\n ";
                    unset($consumer);
                    throw new \Swoole\Exception("ERROR CONNECTING TO KAFKA BROKER", 500);
                    return;
                }

                while (true) {
                    $messageReceived = $consumer->consume($messageClient::TIMEOUT * 1000);
                    switch ($messageReceived->err) {
                        case RD_KAFKA_RESP_ERR_NO_ERROR:
                            foreach ($this->subscriptionProvider->getSubscriptions() as $item) {

                                if ($messageReceived->topic_name === $item['topic']) {

                                    $message = new Message(
                                        payload: $messageReceived->payload,
                                        topic: $messageReceived->topic_name,
                                        createdAt: (new DateTime())->setTimestamp((int)substr((string)$messageReceived->timestamp, 0, 10)),
                                        error: $messageReceived->err,
                                        key: $messageReceived->key,
                                        headers: $messageReceived->headers,
                                    );

                                    go(function () use ($message, $item) {
                                        $class = \Boilerwork\System\Container\Container::getInstance()->get($item['target']);
                                        call_user_func($class, $message);
                                    });
                                }
                            }
                            break;
                        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                            echo "Kafka: No more messages; will wait for more\n";
                            break;
                        case RD_KAFKA_RESP_ERR__TIMED_OUT:
                            // echo "Kafka: Timed out\n";
                            break;
                        default:
                            error($messageReceived->errstr());
                            // var_dump($messageReceived);
                            throw new \Swoole\Exception($messageReceived->errstr(), $messageReceived->err);
                            break;
                    }
                }
            },
            enableCoroutine: true
        ));
    }


    public function process(): Process
    {
        return $this->process;
    }
}
