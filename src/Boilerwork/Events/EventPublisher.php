#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Events;

use Ds\Queue;
use Ds\Vector;
use Boilerwork\Domain\DomainEvent;
use Boilerwork\Helpers\Singleton;
use Boilerwork\System\Clients\MessagingClient;
use RuntimeException;
use Throwable;

final class EventPublisher
{
    use Singleton;

    // private MessagingClient $messagingClient;

    private function __construct(
        private Vector $subscribers = new Vector(),
        private Queue $events = new Queue(),
    ) {
    }

    public function subscribe($eventSubscriber): void
    {
        if (class_exists($eventSubscriber)) {
            $this->subscribers->push($eventSubscriber);
        } else {
            // error(sprintf('%s: %s class does not exist', __class__, $eventSubscriber), RuntimeException::class);
        }
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
        foreach ($this->subscribers as $subscriber) {
            $class = new $subscriber();

            // Ds\Queue -> destructive iteration
            foreach ($this->events as $event) {
                if ($class->isSubscribedTo() === $event::class) {
                    go(function () use ($class, $event) {
                        try {
                            if ($event->isPublic()) {

                                $messagingClient = new MessagingClient();

                                $messagingClient->publish(
                                    message: json_encode($event->serialize()),
                                    queue: $event->getQueue(),
                                    exchange: $event->getExchange()
                                );
                            }
                            $class->handle($event);
                            // (\Boilerwork\System\Container\Container::getInstance()->get($class))->handle($event);
                        } catch (RuntimeException $e) {
                            // error($e->getMessage(), RuntimeException::class);
                        } catch (Throwable $e) {
                            // error($e->getMessage());
                        }
                    });
                }
            }

            unset($class);
        }

        // Clear events to assure events queue is emptied though non existing subscribers
        $this->clearEvents();
    }

    public function clearEvents(): void
    {
        $this->events->clear();
    }
}
