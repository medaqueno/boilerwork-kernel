#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Events;

use Boilerwork\Tracking\TrackContextNotFoundException;
use Boilerwork\Tracking\TrackingContext;
use DateTimeImmutable;

abstract class AbstractEvent
{
    protected string $topic;

    protected ?array $serializedData = null;

    abstract public function id(): string;

    abstract public function serialize(): array;

    abstract public static function unserialize(array $event): self;

    public function wrapSerialize(array $data): array
    {
        /**
         * @var TrackingContext $trackingContext
         */
        $trackingContext = container()->has(TrackingContext::NAME) ? container()->get(TrackingContext::NAME) : throw new TrackContextNotFoundException();

        if ($this->serializedData !== null) {
            return $this->serializedData;
        }

        return $this->serializedData = [
            'id' => $this->id(),
            // 'aggregateVersion' => $this->getAggregateVersion(),
            'type' => static::class,
            'ocurredOn' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM), // Will be removed
            'occurredOn' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM),
            'data' => $data,
            'metadata' => [
                'trackingContext' => $trackingContext->toArray()
            ],
        ];
    }

    public function topic(): ?string
    {
        return $this->topic;
    }
}
