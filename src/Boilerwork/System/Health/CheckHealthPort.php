#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Health;

use Boilerwork\Infra\Http\AbstractHttpPort;
use Boilerwork\Infra\Http\Request;
use Boilerwork\Infra\Http\Response;
use Psr\Http\Message\ResponseInterface;

final class CheckHealthPort extends AbstractHttpPort
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        return Response::json([
            'appName' => $_ENV['APP_NAME'],
            'status' => 'OK'
        ]);
    }
}
