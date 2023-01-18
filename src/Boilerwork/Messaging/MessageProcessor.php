#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Messaging;

use Boilerwork\Container\IsolatedContainer;
use DateTime;

final class MessageProcessor
{
    private MessageClientInterface $messageClient;

    public function __construct(
        private readonly MessagingProviderInterface $subscriptions,
        private readonly array $topics
    ) {
        $isolatedContainer = new IsolatedContainer;
        globalContainer()->setIsolatedContainer($isolatedContainer);

        $this->messageClient = globalContainer()->get(MessageClientInterface::class);
    }

    public function process(): void
    {
        $consumer = $this->messageClient->subscribe(topics: $this->topics);

        if ($consumer === null) {
            echo "\n\n########\nERROR CONNECTING TO KAFKA BROKER\n########\n\n ";
            unset($consumer);
            throw new \OpenSwoole\Exception("ERROR CONNECTING TO KAFKA BROKER", 500);
            return;
        }

        while (true) {
            $messageReceived = $consumer->consume($this->messageClient->timeout() * 1000);

            switch ($messageReceived->err) {
                case \RD_KAFKA_RESP_ERR_NO_ERROR:

                    foreach ($this->subscriptions->getSubscriptions() as $item) {

                        $topicReceived = explode('__', $messageReceived->topic_name)[1];

                        // Empty Payload checks if message is a test or only used to pre-create topic
                        if ($topicReceived === $item['topic'] && $messageReceived->payload !== '') {

                            $message = new Message(
                                payload: $messageReceived->payload,
                                topic: $messageReceived->topic_name,
                                createdAt: (new DateTime())->setTimestamp((int)substr((string)$messageReceived->timestamp, 0, 10)),
                                error: $messageReceived->err,
                                key: $messageReceived->key,
                                headers: $messageReceived->headers,
                            );

                            $class = globalContainer()->get($item['target']);
                            try {
                                call_user_func($class, $message);
                            } catch (\Throwable $th) {
                                error(
                                    sprintf(
                                        'ERROR PROCESSING MESSAGE RECEIVED: %s || Error Message: %s',
                                        json_encode($messageReceived),
                                        $th->getMessage()
                                    ),
                                );
                            }
                        }
                    }
                    break;
                case \RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "Kafka: No more messages; will wait for more\n";
                    break;
                case \RD_KAFKA_RESP_ERR__TIMED_OUT:
                    // echo "Kafka: Timed out\n";
                    break;
                default:
                    error($messageReceived->errstr());
                    // var_dump($messageReceived);
                    throw new \OpenSwoole\Exception($messageReceived->errstr(), $messageReceived->err);
                    break;
            }
        }
    }
}
