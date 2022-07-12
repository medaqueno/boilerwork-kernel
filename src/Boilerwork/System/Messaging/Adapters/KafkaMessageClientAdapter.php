#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Messaging\Adapters;

use Boilerwork\System\Messaging\MessagingClientInterface;

/**
 * Uses rdkafka extension and librdkafka library in order to work.
 *
 * @see https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/index.html
 */
class KafkaMessageClientAdapter implements MessagingClientInterface
{
    const TIMEOUT = 60; // Seconds

    private bool $isWorking = true;

    public function publish(string $message, string $topic): void
    {
        if ($this->isWorking === false) {
            throw new \Swoole\Exception("ERROR CONNECTING TO KAFKA BROKER", 500);
        }

        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', $_ENV['MESSAGE_BROKER_HOST'] . ':' . $_ENV['MESSAGE_BROKER_PORT']);

        //If you need to produce exactly once and want to keep the original produce order, uncomment the line below
        $conf->set('enable.idempotence', 'true');

        // $conf->set('transactional.id', 'HERE_ID');

        $producer = new \RdKafka\Producer($conf);

        $topic = $producer->newTopic($topic);

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        $producer->poll(0);

        for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
            $result = $producer->flush(10000);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new \RuntimeException('Was unable to flush, messages might be lost!');
        }
    }

    public function subscribe(array $topics): ?\RdKafka\KafkaConsumer
    {
        if ($this->isWorking === false) {
            throw new \Swoole\Exception("ERROR CONNECTING TO KAFKA BROKER", 500);
            return null;
        }

        $conf = new \RdKafka\Conf();

        // Set a rebalance callback to log partition assignments (optional)
        $conf->setRebalanceCb(function (\RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    echo "Assign: ";
                    var_dump($partitions);
                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    echo "Revoke: ";
                    var_dump($partitions);
                    $kafka->assign(NULL);
                    break;

                default:
                    throw new \Exception($err);
            }
        });

        // Configure the group.id. All consumer with the same group.id will consume
        // different partitions.
        $conf->set('group.id', $_ENV['APP_ENV'] . '-' .  $_ENV['APP_NAME']);

        // Initial list of Kafka brokers
        $conf->set('metadata.broker.list', $_ENV['MESSAGE_BROKER_HOST'] . ':' . $_ENV['MESSAGE_BROKER_PORT']);

        // Set where to start consuming messages when there is no initial offset in
        // offset store or the desired offset is out of range.
        // 'earliest': start from the beginning
        $conf->set('auto.offset.reset', 'latest');

        $consumer = new \RdKafka\KafkaConsumer($conf);

        try {
            $partitionsInfo = [];
            foreach ($consumer->getMetadata(true, null, 10000)->getTopics() as $topic) {
                $partitionsInfo[$topic->getTopic()] = count($topic->getPartitions());
            }
        } catch (\Throwable $th) {
            $this->isWorking = false;
            return null;
        }

        // Subscribe to topic
        $consumer->subscribe(array_unique($topics));

        echo "Waiting for Kafka partition assignment... (make take some time when\n";
        echo "quickly re-joining the group after leaving it.)\n";

        return $consumer;
    }
}
