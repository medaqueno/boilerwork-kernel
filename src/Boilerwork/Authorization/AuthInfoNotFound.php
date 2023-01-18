#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authorization;

use Boilerwork\Authorization\AuthorizationsProvider;

final readonly class AuthInfoNotFound extends AuthInfo
{
    public function __construct()
    {
    }

    public function hasAuthorization(array $allowedAuthorizations): bool
    {
        $result = array_filter(
            $allowedAuthorizations,
            function ($item) {
                return $item === AuthorizationsProvider::PUBLIC;
            }
        );

        return count($result) > 0;
    }

    public function toArray(): array
    {
        return [];
    }
}
