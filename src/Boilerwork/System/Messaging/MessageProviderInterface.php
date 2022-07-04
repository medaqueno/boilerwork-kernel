#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Messaging;

interface MessageProviderInterface
{
    public function getSubscriptions(): array;
}
