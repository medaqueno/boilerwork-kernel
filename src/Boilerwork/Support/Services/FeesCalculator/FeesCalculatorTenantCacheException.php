#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator;

use Boilerwork\Messaging\MessagePublisher;
use Boilerwork\Support\Exceptions\CustomException;
use Boilerwork\Support\Services\FeesCalculator\Events\TenantCacheNotFound;

final class FeesCalculatorTenantCacheException extends CustomException
{
    public function __construct(string $tenantId, string $customCode, string $customMessage)
    {
        $messagePublisher = MessagePublisher::getInstance();
        //borramos evento
        $messagePublisher->clearEvents();
        $event = new TenantCacheNotFound(
            tenantId: $tenantId
        );
        $messagePublisher->raise($event);
        $messagePublisher->releaseEvents();

        parent::__construct($customCode, $customMessage, 404);
    }
}
