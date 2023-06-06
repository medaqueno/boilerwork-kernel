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
use OpenSwoole\Core\Coroutine\WaitGroup;
use OpenSwoole\Coroutine;
use Psr\Http\Message\ResponseInterface;


final class CheckHealthPort
{
    public function __construct(
        private readonly WritesRepository     $writes,
        private readonly ReadsRepository      $reads,
        private readonly RedisAdapter         $redis,
        private readonly ElasticSearchAdapter $elastic,
    )
    {
    }

    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        $checks = [
            'check_reads_db' => fn() => $this->checkReadsDB(),
            'check_writes_db' => fn() => $this->checkWritesDB(),
            'check_redis' => fn() => $this->checkRedis(),
            'check_elastic' => fn() => $this->checkElastic(),
        ];

        $timeout = 10;
        $status = [];
        $overallStatus = 'OK';

        $wg = new WaitGroup();

        foreach ($checks as $checkName => $checkFunction) {
            $wg->add();
            go(function () use ($wg, $checkName, $checkFunction, &$status, &$overallStatus) {
                $result = $this->executeCheck($checkFunction);
                $status[$checkName] = $result ? 'ok' : 'ko';
                if ($result instanceof \Exception) {
                    $status['error'][$checkName] = $result->getMessage();
                    $overallStatus = 'KO';
                }
                $wg->done();
            });
        }

        $start = microtime(true);
        while (!$wg->wait($timeout)) {
            if (microtime(true) - $start >= $timeout) {
                $overallStatus = 'TimeOut';
                break;
            }
        }

        return Response::json([
            'appName' => env('APP_NAME'),
            'data' => $status,
            'status' => $overallStatus,
        ], $overallStatus === 'OK' ? 200 : ($overallStatus === 'TimeOut' ? 504 : 500)
        );
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

    private function checkReadsDB(): bool
    {
        return $this->reads->queryBuilder->select('1+1 as test')->fetchValue() === 2;
    }

    private function checkWritesDB(): bool
    {
        return $this->writes->queryBuilder->select('1+1 as test')->fetchValue() === 2;
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
