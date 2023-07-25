#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Time;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;
use DateTimeImmutable;
use DateTimeZone;

final class DateTime extends ValueObject
{
    private function __construct(
        private readonly DateTimeImmutable $value
    ) {
        Assert::lazy()->tryAll()
            ->that($value)
            ->isInstanceOf(DateTimeImmutable::class, 'Value must be an instance of DateTimeImmutable', 'dateTime.invalidType')
            ->verifyNow();
    }

    /**
     * Returns the current date and time in the specified timezone.
     *
     * The default timezone is UTC.
     *
     * @return self
     */
    public static function now(): self
    {
        return new self(value: new DateTimeImmutable('now', new DateTimeZone('UTC')));
    }

    /**
     * Creates a new DateTime from the given string and timezone.
     *
     * @param string $value The date time string.
     * @param string $timezone The timezone. Default is 'UTC'.
     *
     * @return self
     */
    public static function fromString(string $value, string $timezone = 'UTC'): self
    {
        return new self(value: new DateTimeImmutable($value, new DateTimeZone($timezone)));
    }

    /**
     * Creates a new DateTime from the given timestamp and timezone.
     *
     * @param int $timestamp The timestamp.
     * @param string $timezone The timezone. Default is 'UTC'.
     *
     * @return self
     */
    public static function fromTimestamp(int $timestamp, string $timezone = 'UTC'): self
    {
        return new self(value: (new DateTimeImmutable())->setTimestamp($timestamp)->setTimezone(new DateTimeZone($timezone)));
    }

    /**
     * Converts the DateTime to an Atom string.
     *
     * @return string The date time in Atom format.
     */
    public function toAtom(): string
    {
        return $this->value->format(DateTimeImmutable::ATOM);
    }

    /**
     * Converts the DateTime to a string.
     *
     * @return string The date time as a string.
     */
    public function toString(): string
    {
        return $this->toAtom();
    }

    /**
     * Returns the underlying DateTimeImmutable object.
     *
     * @return DateTimeImmutable The underlying DateTimeImmutable object.
     */
    public function toDateTimeImmutable(): DateTimeImmutable
    {
        return $this->value;
    }

    /**
     * Converts the DateTimeImmutable to a DateTime object.
     *
     * @return \DateTime The DateTime object.
     */
    public function toDateTime(): \DateTime
    {
        return \DateTime::createFromImmutable($this->value);
    }

    /**
     * Converts the datetime to UTC.
     *
     * If the datetime is already in UTC, this method will simply return the datetime.
     *
     * @return self
     */
    public function convertToUtc(): self
    {
        if ($this->value->getTimezone()->getName() !== 'UTC') {
            return new self(value: $this->value->setTimezone(new DateTimeZone('UTC')));
        }

        return $this;
    }
}