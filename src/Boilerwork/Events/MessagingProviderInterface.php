#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Events;

interface MessagingProviderInterface
{
    public function getSubscriptions(): array;
}
