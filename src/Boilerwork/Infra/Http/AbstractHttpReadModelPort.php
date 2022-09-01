#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Http;

use Psr\Http\Message\ResponseInterface;
use Boilerwork\Infra\Http\Request;

abstract class AbstractHttpReadModelPort
{
    abstract public function __invoke(Request $request, array $vars): ResponseInterface;
}
