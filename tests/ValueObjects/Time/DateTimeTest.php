#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Support\ValueObjects\Time\DateTime;
use Boilerwork\Validation\CustomAssertionFailedException;
use PHPUnit\Framework\TestCase;

final class DateTimeTest extends TestCase
{
    public function testFromString(): void
    {
        $dateTimeString = '2023-04-08T12:34:56+00:00';
        $dateTime = DateTime::fromString($dateTimeString);

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertSame($dateTimeString, $dateTime->toAtom());
    }

    public function testFromTimestamp(): void
    {
        $timestamp = 1680712956;
        $dateTime = DateTime::fromTimestamp($timestamp);

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertSame($timestamp, $dateTime->toDateTimeImmutable()->getTimestamp());
    }

    public function testToStringShouldReturnAtomFormat(): void
    {
        $dateTimeString = '2023-04-08T12:34:56+00:00';
        $dateTime = DateTime::fromString($dateTimeString);

        $this->assertSame($dateTime->toAtom(), $dateTime->toString());
    }

    public function testInvalidString(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to parse time string');

        $invalidDateTimeString = 'InvalidDateTimeString';
        DateTime::fromString($invalidDateTimeString);
    }

    public function testInvalidTimestamp(): void
    {
        $this->expectException(\TypeError::class);

        $invalidTimestamp = 'InvalidTimestamp';
        DateTime::fromTimestamp($invalidTimestamp);
    }

    public function testInvalidTimezone(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('DateTimeZone::__construct(): Unknown or bad timezone');

        $dateTimeString = '2023-04-08T12:34:56+00:00';
        $invalidTimezone = 'InvalidTimezone';
        DateTime::fromString($dateTimeString, $invalidTimezone);
    }
}
