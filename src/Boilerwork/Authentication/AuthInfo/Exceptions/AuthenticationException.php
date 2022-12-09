#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authentication\AuthInfo\Exceptions;

use Boilerwork\Support\Exceptions\CustomException;

class AuthenticationException extends CustomException
{
    public function __construct()
    {
        parent::__construct('authentication.notFound', 'Authentication Not Found', 401);
    }
}
