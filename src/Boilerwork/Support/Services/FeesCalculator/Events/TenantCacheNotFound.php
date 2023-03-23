#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator\Events;

use Boilerwork\Events\AbstractEvent;

final class TenantCacheNotFound extends AbstractEvent
{
    protected string $topic = "exception-tenant-cache-not-found";

    public function __construct(
        public readonly string $tenantId
    ) {
    }

    public function id(): string
    {
        return $this->tenantId;
    }

    public function serialize(): array
    {
        return $this->wrapSerialize(
            data: [
                'tenantId' => $this->tenantId
            ]
        );
    }

    public static function unserialize(array $event): self
    {
        return (new static(
            tenantId: $event['data']['tenantId']
        ));
    }
}
