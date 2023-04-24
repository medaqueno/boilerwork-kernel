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

final class CheckHealthPort
{
    public function __construct(
        private readonly WritesRepository $writes,
        private readonly ReadsRepository $reads,
        private readonly RedisAdapter $redis,
        private readonly ElasticSearchAdapter $elastic,
    ) {
    }

    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        try {

            $checkReadsDB  = $this->reads->queryBuilder->select('1+1 as test')->fetchValue();
            if ($checkReadsDB !== 2) {
                throw new \Exception('Error queriying Reads DB', 500);
            }
            $checkWritesDB = $this->writes->queryBuilder->select('1+1 as test')->fetchValue();
            if ($checkWritesDB !== 2) {
                throw new \Exception('Error queriying Writes DB', 500);
            }

            $checkRedis = $this->redis->rawCommand('ping');
            if ($checkRedis !== true) {
                throw new \Exception('Error connecting Redis', 500);
            }

            $checkElastic = $this->elastic->raw()->ping()->asBool();
            if ($checkElastic !== true) {
                throw new \Exception('Error connecting Elastic', 500);
            }
        } catch (\Exception $exception) {
            return Response::json([
                'appName' => env('APP_NAME'),
                'data'    => [
                    'check_writes_db' => $checkWritesDB === 2 ? 'ok' : 'ko',
                    'check_reads_db'  => $checkReadsDB === 2 ? 'ok' : 'ko',
                    'check_redis'  => $checkRedis ? 'ok' : 'ko',
                    'check_elastic'  => $checkElastic ? 'ok' : 'ko',
                ],
                'error'   => [
                    'exception' => $exception->getMessage(),
                ],
                'status'  => 'KO',
            ], 500);
        }

        return Response::json([
            'appName' => env('APP_NAME'),
            'data'    => [
                'data' => [
                    'check_writes_db' => $checkWritesDB === 2 ? 'ok' : 'ko',
                    'check_reads_db'  => $checkReadsDB === 2 ? 'ok' : 'ko',
                    'check_redis'  => $checkRedis ? 'ok' : 'ko',
                    'check_elastic'  => $checkElastic ? 'ok' : 'ko',
                ],
            ],
            'status'  => 'OK',
        ], 200);
    }
}
