#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authentication\AuthInfo\Exceptions;

use OpenSwoole\Exception;

class AuthInfoException extends Exception
{
    public function __construct()
    {
        parent::__construct('Auth Info is not complete', 401);
    }
}
