#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\AuthInfo;


final class AuthInfoNotFound extends AuthInfo
{
    public function __construct()
    {
    }

    public function hasPermission(string $permission): bool
    {
        return false;
    }

    public function serialize(): array
    {
        return [];
    }
}
