#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Messaging;

/**
 * Binded in Application container as singleton.
 *
 * This interface must be instantiated instead instead of any adapter.
 */
interface MessagingClientInterface
{
    public function publish(string $message, string $topic): void;

    public function subscribe(array $topics);
}
