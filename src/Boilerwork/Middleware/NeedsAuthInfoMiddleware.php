#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Middleware;

use Boilerwork\Authentication\AuthInfo\AuthInfoNotFound;
use Boilerwork\Authentication\AuthInfo\Exceptions\AuthInfoException;
use Boilerwork\Http\Request;
use function Boilerwork\Infra\Http\Middleware\getAuthInfo;

final class NeedsAuthInfoMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request): Request
    {
        if (getAuthInfo() instanceof AuthInfoNotFound) {
            throw new AuthInfoException();
        }

        return $request;
    }
}
