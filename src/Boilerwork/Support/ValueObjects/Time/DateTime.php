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

    public static function now(string $timezone = 'UTC'): self
    {
        return new self(value: new DateTimeImmutable('now', new DateTimeZone($timezone)));
    }

    public static function fromString(string $value, string $timezone = 'UTC'): self
    {
        return new self(value: new DateTimeImmutable($value, new DateTimeZone($timezone)));
    }

    public static function fromTimestamp(int $timestamp, string $timezone = 'UTC'): self
    {
        return new self(value: (new DateTimeImmutable())->setTimestamp($timestamp)->setTimezone(new DateTimeZone($timezone)));
    }

    public function toAtom(): string
    {
        return $this->value->format(DateTimeImmutable::ATOM);
    }

    public function toString(): string
    {
        return $this->toAtom();
    }

    public function toDateTimeImmutable(): DateTimeImmutable
    {
        return $this->value;
    }

    public function toDateTime(): \DateTime
    {
        return \DateTime::createFromImmutable($this->value);
    }
}