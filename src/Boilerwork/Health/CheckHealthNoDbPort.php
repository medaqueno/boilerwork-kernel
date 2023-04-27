#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Health;

use Boilerwork\Http\Request;
use Boilerwork\Http\Response;
use Boilerwork\Persistence\Adapters\ElasticSearch\ElasticSearchAdapter;
use Boilerwork\Persistence\Adapters\Redis\RedisAdapter;
use Boilerwork\Persistence\Repositories\ReadsRepository;
use Boilerwork\Persistence\Repositories\WritesRepository;
use Psr\Http\Message\ResponseInterface;

final class CheckHealthNoDbPort
{
    public function __construct(
        private readonly RedisAdapter $redis,
        private readonly ElasticSearchAdapter $elastic,
    ) {
    }

    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        $checks = [
            'check_redis' => fn() => $this->checkRedis(),
            'check_elastic' => fn() => $this->checkElastic(),
        ];

        $status = [];
        $overallStatus = 'OK';

        foreach ($checks as $checkName => $checkFunction) {
            $result = $this->executeCheck($checkFunction);
            $status[$checkName] = $result ? 'ok' : 'ko';
            if ($result instanceof \Exception) {
                $status['error'][$checkName] = $result->getMessage();
                $overallStatus = 'KO';
            }
        }

        return Response::json([
            'appName' => env('APP_NAME'),
            'data'    => $status,
            'status'  => $overallStatus,
        ], $overallStatus === 'OK' ? 200 : 500);
    }

    private function executeCheck(callable $checkFunction): bool|\Exception
    {
        try {
            return $checkFunction();
        } catch (\Exception $exception) {
            logger($exception->getMessage());
            return $exception;
        }
    }

    private function checkRedis(): bool
    {
        return $this->redis->rawCommand('ping');
    }

    private function checkElastic(): bool
    {
        return $this->elastic->raw()->ping()->asBool();
    }
}
