#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Jobs;

use Boilerwork\Container\IsolatedContainer;
use Boilerwork\Persistence\Adapters\Redis\RedisClient;
use Boilerwork\Server\IsProcessInterface;
use DateTime;
use GO\Job;
use GO\Scheduler;
use Swoole\Process;


/**
 * We are using GO\Scheduler to check due times and executing jobs
 * @uses https://github.com/peppeocchi/php-cron-scheduler
 *
 *
 */
final class JobScheduler implements IsProcessInterface
{
    private Process $process;

    private const LOOP_INTERVAL = 60; // Execute each 60 seconds as is the default cron job interval

    private Scheduler $scheduler;
    private RedisClient $client;

    public function __construct(
        private $jobsProvider
    ) {
        $isolatedContainer = new IsolatedContainer;
        globalContainer()->setIsolatedContainer($isolatedContainer);

        $this->scheduler = new Scheduler();
        $this->client = new RedisClient();

        $this->addJobs();

        $this->process = (new Process(
            function () {

                while (true) {
                    $this->check();
                    // Use native sleep only with Swoole hooks enabled
                    sleep(self::LOOP_INTERVAL);
                }
            },
            enableCoroutine: true
        ));
    }

    public function process(): Process
    {
        return $this->process;
    }

    /**
     * Check is job is Due an must be executed
     */
    private function check()
    {
        foreach ($this->scheduler->getQueuedJobs() as $job) {
            if ($job->isDue(new DateTime()) === true) {

                $this->executeIfNotOverlapped($job);
            }
        }
    }

    /**
     * Check in Redis if Job is already beeing processed to aboid overlapping with
     * other posible instances of the same microservice.
     */
    private function executeIfNotOverlapped(Job $job): void
    {
        go(function () use ($job) {
            $jobId = $job->getId();

            $this->client->getConnection();

            if ($this->client->get($jobId) === false) {

                $this->client->set($jobId, 'JobInProgress');

                $this->client->putConnection();

                logger(sprintf("JOBS - Executing %s", $jobId));

                $job->run();

                logger(sprintf("JOBS -Job %s Output: %s", $jobId, $job->getOutput()));

                $this->client->getConnection();

                $this->client->del($jobId);
            }

            $this->client->putConnection();
        });
    }

    private function addJobs(): void
    {
        foreach ($this->jobsProvider->jobs as $job) {

            $class = globalContainer()->get($job[0]);

            $task =  $this->scheduler->call(
                fn: [$class, 'handle'],
                id: $class::class,
            );

            $interval = $job[1][0];
            $args = $job[1][1] ?: null;

            if ($args !== null && $args !== '') {
                $task->$interval($args);
                return;
            }

            $task->$interval();
        }
    }
}
