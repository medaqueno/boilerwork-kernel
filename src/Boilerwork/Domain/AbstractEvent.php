#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

use DateTimeImmutable;

abstract class AbstractEvent implements DomainEvent
{
    protected string $topic;

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

    public function getTopic(): ?string
    {
        return $this->topic;
    }
}
