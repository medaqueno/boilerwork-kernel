#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Http;

use Psr\Http\Message\ResponseInterface;
use Boilerwork\System\Http\Request;

abstract class AbstractHttpViewModelPort
{
    abstract public function __invoke(Request $request, array $vars): ResponseInterface;
}
