#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Http\Middleware;

use Boilerwork\System\AuthInfo\AuthInfoNotFound;
use Boilerwork\System\AuthInfo\Exceptions\AuthInfoException;
use Boilerwork\System\Http\Middleware\MiddlewareInterface;
use Boilerwork\System\Http\Request;

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
