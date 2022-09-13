#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

use DateTimeImmutable;

abstract class AbstractEvent
{
    protected string $topic;

    abstract public function aggregateId(): string;

    abstract public function serialize(): array;

    abstract public static function unserialize(array $event): self;

    public function wrapSerialize(array $data): array
    {
        return [
            'aggregateId' => $this->aggregateId(),
            // 'aggregateVersion' => $this->getAggregateVersion(),
            'type' => static::class,
            'ocurredOn' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM),
            'data' => $data,
            'metadata' => authInfo()->serialize(),
        ];
    }

    public function topic(): ?string
    {
        return $this->topic;
    }
}
