#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes\Crontab;

use App\Shared\Providers\MessagingProvider;
use Attribute;
use Boilerwork\Persistence\Adapters\Redis\RedisAdapter;
use Boilerwork\Validation\Assert;
use OpenSwoole\Coroutine;

#[Attribute(Attribute::TARGET_CLASS)]
final class Crontab
{
    /**
     * @const INTERVAL_EVERY_MINUTE Executes the task every minute.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_EVERY_MINUTE);
     */
    const INTERVAL_EVERY_MINUTE = 'everyMinute';

    /**
     * @const INTERVAL_HOURLY Executes the task every hour at 00:00.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_HOURLY);
     */
    const INTERVAL_HOURLY = 'hourly';

    /**
     * @const INTERVAL_DAILY Executes the task every day at 00:00.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_DAILY);
     */
    const INTERVAL_DAILY = 'daily';

    /**
     * @const INTERVAL_DAILY_AT Executes the task every day at the specific time.
     *                          Should be used along with the $at parameter.
     *                          Be aware of time zones.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_DAILY_AT, at: '23:54');
     */
    const INTERVAL_DAILY_AT = 'dailyAt';

    /**
     * @const INTERVAL_WEEKLY Executes the task every week on Monday at midnight.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_WEEKLY);
     */
    const INTERVAL_WEEKLY = 'weekly';
    /**
     * @const INTERVAL_MONDAY Executes the task every Monday at midnight.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_MONDAY);
     */
    const INTERVAL_MONDAY = 'monday';
    /**
     * @const INTERVAL_TUESDAY Executes the task every Tuesday at midnight.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_TUESDAY);
     */
    const INTERVAL_TUESDAY = 'tuesday';
    /**
     * @const INTERVAL_WEDNESDAY Executes the task every Wednesday at midnight.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_WEDNESDAY);
     */
    const INTERVAL_WEDNESDAY = 'wednesday';
    /**
     * @const INTERVAL_THURSDAY Executes the task every Thursday at midnight.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_THURSDAY);
     */
    const INTERVAL_THURSDAY = 'thursday';
    /**
     * @const INTERVAL_FRIDAY Executes the task every Friday at midnight.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_FRIDAY);
     */
    const INTERVAL_FRIDAY = 'friday';
    /**
     * @const INTERVAL_SATURDAY Executes the task every Saturday at midnight.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_SATURDAY);
     */
    const INTERVAL_SATURDAY = 'saturday';
    /**
     * @const INTERVAL_SUNDAY Executes the task every Sunday at midnight.
     *
     * Example usage:
     * Crontab(interval: Crontab::INTERVAL_SUNDAY);
     */
    const INTERVAL_SUNDAY = 'sunday';

    public function __construct(
        public readonly string  $interval,
        public readonly ?string $at = null,
        public readonly ?string $target = null
    )
    {
    }

}
