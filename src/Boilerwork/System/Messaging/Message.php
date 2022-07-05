#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Messaging;

use DateTimeInterface;

final class Message
{
    public function __construct(
        public readonly string $payload,
        public readonly string $topic,
        public readonly DateTimeInterface $createdAt,
        public readonly mixed $error,
        public readonly ?string $key,
        public readonly array $headers,
    ) {
    }

    public function getParsedPayload()
    {
        return json_decode($this->payload);
    }
}
