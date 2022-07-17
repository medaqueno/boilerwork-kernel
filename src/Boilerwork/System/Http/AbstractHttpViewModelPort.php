#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Http;

use Boilerwork\Application\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Boilerwork\System\Http\Request;

abstract class AbstractHttpPort
{
    abstract public function __invoke(Request $request, array $vars): ResponseInterface;

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
