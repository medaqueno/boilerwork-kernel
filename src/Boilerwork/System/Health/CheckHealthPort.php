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
            'status' => 'OK',
            'appName' => isset($_ENV['APP_NAME']) ? $_ENV['APP_NAME'] : 'nop',
            'MESSAGE_BROKER_HOST' => isset($_ENV['MESSAGE_BROKER_HOST']) ? $_ENV['MESSAGE_BROKER_HOST'] : 'nope',
            'POSTGRESQL_READS_HOST' => isset($_ENV['POSTGRESQL_READS_HOST']) ? $_ENV['POSTGRESQL_READS_HOST'] : 'nope',
            'POSTGRESQL_READS_USERNAME' => isset($_ENV['POSTGRESQL_READS_USERNAME']) ? $_ENV['POSTGRESQL_READS_USERNAME'] : 'nope',
            'POSTGRESQL_READS_PASSWORD' => isset($_ENV['POSTGRESQL_READS_PASSWORD']) ? $_ENV['POSTGRESQL_READS_PASSWORD'] : 'nope',
            'POSTGRESQL_WRITES_HOST' => isset($_ENV['POSTGRESQL_WRITES_HOST']) ? $_ENV['POSTGRESQL_WRITES_HOST'] : 'nope',
            'POSTGRESQL_WRITES_USERNAME' => isset($_ENV['POSTGRESQL_WRITES_USERNAME']) ? $_ENV['POSTGRESQL_WRITES_USERNAME'] : 'nope',
            'POSTGRESQL_WRITES_PASSWORD' => isset($_ENV['POSTGRESQL_WRITES_PASSWORD']) ? $_ENV['POSTGRESQL_WRITES_PASSWORD'] : 'nope',
        ]);
    }
}
