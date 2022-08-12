#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Messaging;

interface MessagingProviderInterface
{
    public function getSubscriptions(): array;
}
