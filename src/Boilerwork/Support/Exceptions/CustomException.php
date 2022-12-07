#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Exceptions;

abstract class CustomException extends \InvalidArgumentException
{
    public function __construct(string $code, string $message, int $httpStatus)
    {
        parent::__construct(json_encode(
            [
                "error" =>
                [
                    "code" => $code,
                    "message" => $message,
                    "errors" => []
                ]
            ]
        ), $httpStatus, null);
    }
}
