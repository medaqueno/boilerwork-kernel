#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Events;

use Ds\Queue;
use Ds\Vector;
use Boilerwork\Domain\AbstractEvent;
use Boilerwork\Helpers\Singleton;
use RuntimeException;
use Throwable;

final class EventPublisher
{
    use Singleton;

    private function __construct(
        private Vector $subscribers = new Vector(),
        private Queue $events = new Queue(),
    ) {
    }

    public function raise(AbstractEvent $event): void
    {
        $this->events->push($event);
    }

    /**
     *  Send events to subscriber and pop event in each loop (Ds\Queue)
     **/
    public function releaseEvents(): void
    {
        $messagingClient = container()->get(\Boilerwork\Infra\Messaging\MessagingClientInterface::class);

        // Ds\Queue -> destructive iteration
        foreach ($this->events as $event) {
            // Publish public events as Messages to Brokers
            go(function () use ($event, $messagingClient) {
                try {
                    $messagingClient->publish(
                        message: json_encode($event->serialize()),
                        topic: sprintf('%s__%s', $_ENV['APP_ENV'], $event->topic()),
                    );
                } catch (RuntimeException $e) {
                    error($e->getMessage(), RuntimeException::class);
                } catch (Throwable $e) {
                    error($e->getMessage());
                }
            });
        }

        // Clear events to assure events queue is emptied though non existing subscribers
        $this->clearEvents();
    }

    public function clearEvents(): void
    {
        $this->events->clear();
    }
}
