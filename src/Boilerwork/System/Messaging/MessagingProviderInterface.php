#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Messaging;

interface MessagingProviderInterface
{
    public function getSubscriptions(): array;
}
