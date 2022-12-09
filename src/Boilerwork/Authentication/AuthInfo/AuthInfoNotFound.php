#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authentication\AuthInfo;

use Boilerwork\Authorization\AuthorizationsProvider;

final class AuthInfoNotFound extends AuthInfo
{
    public function __construct()
    {
    }

    public function hasAuthorization(array $allowedAuthorizations): bool
    {
        $result = array_filter(
            $allowedAuthorizations,
            function ($item) {
                return $item === AuthorizationsProvider::PUBLIC->value;
            }
        );

        return count($result) > 0;
    }

    public function serialize(): array
    {
        return [];
    }
}
