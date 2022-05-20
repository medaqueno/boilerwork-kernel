#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use DateTimeImmutable;

abstract class AbstractEvent implements DomainEvent
{
    protected bool $isPublic = false;

    protected ?string $queue;

    protected ?string $exchange;

    public function wrapSerialize(array $data): array
    {
        return [
            'aggregateId' => $this->getAggregateId(),
            // 'aggregateVersion' => $this->getAggregateVersion(),
            'type' => static::class,
            'ocurredOn' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM),
            'data' => $data,
        ];
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function getQueue(): ?string
    {
        return $this->queue;
    }

    public function getExchange(): ?string
    {
        return $this->exchange;
    }
}
