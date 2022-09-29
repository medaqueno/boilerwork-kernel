#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Foundation\ReadModels;

use Boilerwork\Http\Request;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractHttpReadModelPort
{
    abstract public function __invoke(Request $request, array $vars): ResponseInterface;
}
