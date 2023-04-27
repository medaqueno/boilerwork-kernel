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

        return Response::json([
            'appName' => env('APP_NAME'),
            'data'    => [],
            'status'  => 'OK',
        ], 200);
        /*
        $status = [
            'check_writes_db' => 'ko',
            'check_reads_db'  => 'ko',
            'check_redis'     => 'ko',
            'check_elastic'   => 'ko',
            'error'           => '',
        ];

        try {
            $status['check_reads_db'] = $this->checkReadsDB() ? 'ok' : 'ko';
            $status['check_writes_db'] = $this->checkWritesDB() ? 'ok' : 'ko';
            $status['check_redis'] = $this->checkRedis() ? 'ok' : 'ko';
            $status['check_elastic'] = $this->checkElastic() ? 'ok' : 'ko';
        } catch (\Exception $exception) {
            // Log the exception if necessary
            $status['error'] = $exception->getMessage();
            logger($exception->getMessage());
        }

        $overallStatus = 'OK';
        foreach ($status as $check) {
            if ($check === 'ko') {
                $overallStatus = 'KO';
                break;
            }
        }

        return Response::json([
            'appName' => env('APP_NAME'),
            'data'    => $status,
            'status'  => $overallStatus,
        ], $overallStatus === 'OK' ? 200 : 500);*/
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
