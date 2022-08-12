#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Http;

use Boilerwork\Application\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Boilerwork\Infra\Http\Request;

abstract class AbstractHttpPort
{
    abstract public function __invoke(Request $request, array $vars): ResponseInterface;

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
