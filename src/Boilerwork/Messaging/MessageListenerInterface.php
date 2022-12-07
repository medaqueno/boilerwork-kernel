#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Messaging;

interface MessageListenerInterface
{
    public function __invoke(Message $message): void;
}
