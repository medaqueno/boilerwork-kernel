#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\UI;

use Kernel\Application\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Kernel\System\Http\Request;

abstract class AbstractHTTPPort
{
    abstract public function __invoke(Request $request, array $vars): ResponseInterface;

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
