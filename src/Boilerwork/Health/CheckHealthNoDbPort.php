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
        try {

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
                    'check_writes_db' => 'NO DB NEEDED',
                    'check_reads_db'  => 'NO DB NEEDED',
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
                    'check_writes_db' => 'NO DB NEEDED',
                    'check_reads_db'  => 'NO DB NEEDED',
                    'check_redis'  => $checkRedis ? 'ok' : 'ko',
                    'check_elastic'  => $checkElastic ? 'ok' : 'ko',
                ],
            ],
            'status'  => 'OK',
        ], 200);
    }
}
