#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authorization;

use Boilerwork\Support\Exceptions\CustomException;

class AuthorizationException extends CustomException
{
    public function __construct()
    {
        parent::__construct('authorization.forbidden', 'User has not authorization to access this resource', 403);
    }
}
