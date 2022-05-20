#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Jobs;

use DateTimeImmutable;

abstract class AbstractJobScheduler
{
    /**
     * @description Runs every minute
     * Ex: [ExampleJob::class, [self::INTERVAL_EVERY_MINUTE, null]]
     **/
    public const INTERVAL_EVERY_MINUTE = 'everyMinute';

    /**
     * @description Runs every hour at the specified minute
     * Ex: 01 or 56 [ExampleJob::class, [self::INTERVAL_HOURLY_AT_MINUTE, '08']]
     **/
    public const INTERVAL_HOURLY_AT_MINUTE = 'hourlyAtMinute';

    /**
     * @description Runs every hour
     * Ex: [ExampleJob::class, [self::INTERVAL_EVERY_HOUR, null]]
     **/
    public const INTERVAL_EVERY_HOUR = 'everyHour';

    /**
     * @description Runs every day at the specified hour
     * Ex: 03 or 21 [ExampleJob::class, [self::INTERVAL_DAILY_AT_HOUR, '04']]
     **/
    public const INTERVAL_DAILY_AT_HOUR = 'dailyAtHour';

    /**
     * @description Runs every day at the specified Time HH:ss
     * Ex: 23:03 or 2:23 [ExampleJob::class, [self::INTERVAL_DAILY_AT_TIME, '8:04']]
     **/
    public const INTERVAL_DAILY_AT_TIME = 'dailyAtTime';

    /**
     * @description Runs every week at specified day by its correlative number
     * Ex: between 1 (monday) and 7 (sunday) [ExampleJob::class, [self::INTERVAL_EVERY_DAY_OF_WEEK_ISO, '2']]
     **/
    public const INTERVAL_EVERY_DAY_OF_WEEK_ISO = 'everyDayOfWeekIso';

    protected array $jobs = [];

    private array $jobsToBeExecuted = [];

    final public function run(): void
    {
        $this->checkJobs();
    }

    final protected function checkJobs(): void
    {

        // Add to tasksToBeExecuted array if proceeds
        foreach ($this->jobs as $job) {
            // Check if Task if in time to be executed
            if ($this->shouldTrigger($job) === true) {
                // If it is not in task to be executed, add it
                if (!isset($this->jobsToBeExecuted[$job[0]])) {
                    $this->jobsToBeExecuted[$job[0]] = $job[0];
                }
            } else {
                // It is not time, if still exists in task to be executed, remove it
                if (array_key_exists($job[0], $this->jobsToBeExecuted) === true) {
                    unset($this->jobsToBeExecuted[$job[0]]);
                    reset($this->jobsToBeExecuted);
                }
            }
        }

        // Execute tasks in tasksToBeExecuted array
        foreach ($this->jobsToBeExecuted as $jobToExecute) {
            // Task is in time but has not been executed yet
            if ($jobToExecute !== 'executed') {
                $job = app()->container()->get($jobToExecute);

                go(function () use ($jobToExecute, $job) {
                    if ($job instanceof JobInterface) {
                        $job->handle();
                    } else {
                        $message = 'Task ' . $jobToExecute . ' should implement JobInterface';

                        error($message, \RuntimeException::class);

                        throw new \RuntimeException($message);
                    }
                    $this->jobsToBeExecuted[$jobToExecute] = 'executed';
                });
            }
        }
    }

    /**
     * Checks if it is the moment to execute a task.
     **/
    private function shouldTrigger(array $job): bool
    {
        $now = new DateTimeImmutable('now');

        $currentSeconds = (int)$now->format('s');
        $currentMinute = (int)$now->format('i');
        $currentHour = (int)$now->format('H');
        $currentTime = $now->format('H:i');
        $currentDayOfWeekIso = (int)$now->format('N');

        $period = $job[1][0];
        $moment = $job[1][1] ?? null;

        return match ($period) {
            self::INTERVAL_HOURLY_AT_MINUTE => ($currentMinute == $moment),
            self::INTERVAL_EVERY_MINUTE => ($currentSeconds <= 30),
            self::INTERVAL_EVERY_HOUR => ($currentMinute == 0),
            self::INTERVAL_DAILY_AT_HOUR => ($currentHour == $moment),
            self::INTERVAL_DAILY_AT_TIME => ($currentTime == $moment),
            self::INTERVAL_EVERY_DAY_OF_WEEK_ISO => ($currentDayOfWeekIso == $moment),
            default => false,
        };
    }
}
