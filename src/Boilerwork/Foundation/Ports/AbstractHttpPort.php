#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\Ports;

use Boilerwork\Bus\CommandBus;
use Boilerwork\Http\Request;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractHttpPort
{
    abstract public function __invoke(Request $request, array $vars): ResponseInterface;

    final public function command(): CommandBus
    {
        return new CommandBus();
    }
}
