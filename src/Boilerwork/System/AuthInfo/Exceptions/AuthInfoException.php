#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\AuthInfo\Exceptions;

use Swoole\Exception;

class AuthInfoException extends Exception
{
    public function __construct()
    {
        parent::__construct('Auth Info is not complete', 401);
    }
}
