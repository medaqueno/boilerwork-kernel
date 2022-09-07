#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Jobs;

/**
 * We are using GO\Scheduler to check due times and executing jobs
 * @uses https://github.com/peppeocchi/php-cron-scheduler
 * https://github.com/peppeocchi/php-cron-scheduler#schedules-execution-time
 *
 */
abstract class AbstractJobProvider
{
    public array $jobs = [];

    /**
     * Runs by default at second 00
     * string minute can be passed as second argument
     * to specify the job runs every $minute minutes
     */
    protected const INTERVAL_EVERYMINUTE = "everyMinute";

    /**
     * Runs by default at minute 00
     * string minute (04,14,00) can be passed as second argument
     */
    protected const INTERVAL_HOURLY = "hourly";

    /**
     * Runs by default at 00:00
     * string hour:minute can be passed as second argument
     */
    protected const INTERVAL_DAILY = "daily";

    /**
     * Accepts Cron expressions
     *  every minute (* * * * *) by default
     */
    protected const INTERVAL_AT = "at";

    /**
     * Pass datetime as second argument
     * E.g: '2018-01-01 12:20' or '2018-01-01'
     */
    protected const INTERVAL_DATE = "date";

    protected const INTERVAL_MONDAY = "monday";

    protected const INTERVAL_TUESDAY = "tuesday";

    protected const INTERVAL_WEDNESDAY = "wednesday";

    protected const INTERVAL_THURSDAY = "thursday";

    protected const INTERVAL_FRIDAY = "friday";

    protected const INTERVAL_SATURDAY = "saturday";

    protected const INTERVAL_SUNDAY = "sunday";
}
