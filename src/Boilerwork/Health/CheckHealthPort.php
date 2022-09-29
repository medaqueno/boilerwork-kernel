#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Health;

use Boilerwork\Foundation\Ports\AbstractHttpPort;
use Boilerwork\Http\Request;
use Boilerwork\Http\Response;
use Psr\Http\Message\ResponseInterface;

final class CheckHealthPort extends AbstractHttpPort
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        return Response::json([
            'appName' => env('APP_NAME'),
            'status' => 'OK'
        ]);
    }
}
