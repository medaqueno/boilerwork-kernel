#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes\Crontab;

use Attribute;
use Boilerwork\Persistence\Adapters\Redis\RedisAdapter;

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

    private readonly RedisAdapter $redis;

    public function __construct(string $interval, string $at = null, string $target = null)
    {
        go(function () use ($target, $interval, $at) {
            $this->redis = globalContainer()->get(RedisAdapter::class);
            while (true) {
                $intervalSeconds = $this->getSecondsForInterval($interval, $at);

                // Intenta obtener un bloqueo en Redis y establece un TTL en la clave
                $key = sprintf('crontab:%s:%s:%s', env('APP_ENV'), env('APP_NAME'), $target);
                $lockAcquired = $this->redis->set($key, 1, ['nx', 'ex' => $intervalSeconds]);

                if ($lockAcquired) {
                    try {
                        $instance = globalContainer()->get($target);
                        $instance->handle();

                    } catch (\Throwable $e) {
                        $errorMessage = "Error while executing task: " . $e->getMessage();
                        error($errorMessage);
                    }
                }

                // Get remaining TTL
                $remainingTTL = $this->redis->ttl($key);

                // If TTL is -1 (there is no TLL, use complete interval)
                $sleepTime = $remainingTTL === -1 ? $intervalSeconds : $remainingTTL;

                sleep($sleepTime);
            }
        });
    }

    private function getSecondsForInterval(string $interval, string $at = null): int
    {
        return match ($interval) {
            'everyMinute' => $this->getSecondsUntilNextMinute(),
            'hourly' => $this->getSecondsUntilNextHour(),
            'daily',
            'dailyAt' => $this->getSecondsUntilNextDayAt(at: $at),
            'weekly' => $this->getSecondsUntilNextMonday(),
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday' => $this->getSecondsUntilNextDayOfTheWeek(dayOfTheWeek: $interval),
            default => throw new \InvalidArgumentException("Invalid interval: $interval"),
        };
    }

    private function getSecondsUntilNextMinute(): int
    {
        $nextMinute = new \DateTime('1 minute');
        $nextMinute->setTime((int)$nextMinute->format('H'), (int)$nextMinute->format('i'), 0);

        $now = new \DateTime();

        return $nextMinute->getTimestamp() - $now->getTimestamp();
    }

    private function getSecondsUntilNextHour(): int
    {
        $nextHour = new \DateTime('1 hour');
        $nextHour->setTime((int)$nextHour->format('H'), 0, 0);
        $now = new \DateTime();

        return $nextHour->getTimestamp() - $now->getTimestamp();
    }

    /**
     * Calculate the number of seconds until the next occurrence of a specific day of the week.
     *
     * This function calculates the number of seconds between the current time and the next
     * occurrence of the specified day of the week. The day of the week should be provided
     * as a string, such as 'monday', 'tuesday', etc.
     *
     * @param string $dayOfTheWeek The day of the week for which to calculate the next occurrence.
     *                             This should be a string like 'monday', 'tuesday', etc.
     * @return int The number of seconds until the next occurrence of the specified day of the week.
     *
     * @throws \Exception If there is an error parsing the day of the week string.
     *
     * @example
     * // Get the number of seconds until next Monday.
     * $seconds = $this->getSecondsUntilNextDayOfTheWeek('monday');
     *
     * // Get the number of seconds until next Friday.
     * $seconds = $this->getSecondsUntilNextDayOfTheWeek('friday');
     */
    private function getSecondsUntilNextDayOfTheWeek(string $dayOfTheWeek): int
    {
        $nextDay = new \DateTime('next ' . $dayOfTheWeek);
        $now = new \DateTime();

        return $nextDay->getTimestamp() - $now->getTimestamp();
    }

    /**
     * Get remaining secondes until next Monday at 00:00
     */
    private function getSecondsUntilNextMonday(): int
    {
        $nextMonday = new \DateTime('next monday');
        $nextMonday->setTime(0, 0);
        $now = new \DateTime();

        return $nextMonday->getTimestamp() - $now->getTimestamp();
    }

    /**
     * Calculate the number of seconds until the next day at a given time.
     *
     * This function calculates the number of seconds between the current time
     * and the next day at a specific time. If no time is provided, it defaults
     * to midnight. The time should be provided in the format 'HH:MM'.
     *
     * @param string|null $at The time at which the next day should be calculated,
     *                        in the format 'HH:MM'. If null, defaults to '00:00'.
     * @return int The number of seconds until the next day at the specified time.
     *
     * @throws \Exception If there is an error parsing the time string.
     *
     * @example
     * // Get the number of seconds until the next day at midnight.
     * $seconds = $this->getSecondsUntilNextDayAt();
     *
     * // Get the number of seconds until the next day at 13:30.
     * $seconds = $this->getSecondsUntilNextDayAt('13:30');
     */
    private function getSecondsUntilNextDayAt($at = null): int
    {
        $nextMidnight = new \DateTime('tomorrow');

        if ($at) {
            list($hour, $minute) = explode(':', $at);
            $nextMidnight->setTime((int)$hour, (int)$minute, 0);
        } else {
            $nextMidnight->setTime(0, 0, 0);
        }

        $now = new \DateTime();

        return $nextMidnight->getTimestamp() - $now->getTimestamp();
    }
}
