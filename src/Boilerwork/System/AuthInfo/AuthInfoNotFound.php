#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\AuthInfo;

use Boilerwork\Domain\ValueObjects\Identity;

final class AuthInfoNotFound extends AuthInfo
{
    public function __construct()
    {
    }

    public function hasPermission(array $allowedPermissions): bool
    {
        $result = array_filter(
            $allowedPermissions,
            function ($item) {
                return $item === 'Public';
            }
        );

        if (count($result) === 0) {
            throw new \Exception("User is not authenticated", 401);
        }

        return true;
    }

    public function serialize(): array
    {
        return [];
    }
}
