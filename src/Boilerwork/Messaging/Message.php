#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Messaging;

use Boilerwork\Authorization\AuthInfo;
use Boilerwork\Tracking\TrackingContext;
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

    public function parsedPayload()
    {
        return json_decode($this->payload);
    }

    public function trackingContext(): TrackingContext
    {
        $trackingContext = TrackingContext::fromMessage($this->parsedPayload()->metadata->trackingContext->transactionId);
        $trackingContext->addAuthInfo(AuthInfo::fromMessage((array)$this->parsedPayload()->metadata->trackingContext->authInfo));

        return $trackingContext;
    }
}
