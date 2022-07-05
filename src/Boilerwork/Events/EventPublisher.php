#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Events;

use Ds\Queue;
use Ds\Vector;
use Boilerwork\Domain\DomainEvent;
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

    public function raise(DomainEvent $event): void
    {
        $this->events->push($event);
    }

    /**
     *  Send events to subscriber and pop event in each loop (Ds\Queue)
     **/
    public function releaseEvents(): void
    {
        // Ds\Queue -> destructive iteration
        foreach ($this->events as $event) {

            // Publish public events as Messages to Brokers
            go(function () use ($event) {
                try {
                    $messagingClient = \Boilerwork\System\Container\Container::getInstance()->get(\Boilerwork\System\Messaging\MessagingClientInterface::class);

                    $messagingClient->publish(
                        message: json_encode($event->serialize()),
                        topic: $event->getTopic(),
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
