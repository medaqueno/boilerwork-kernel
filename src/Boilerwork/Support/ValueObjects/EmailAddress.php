#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;

/**
 *
 **/
abstract class EmailAddress extends ValueObject
{
    public function __construct(
        private string $value
    ) {
        $this->value = mb_strtolower($value);

        Assert::lazy()->tryAll()
            ->that($this->value)
            ->email('Value must be a valid email', 'email.invalidFormat')
            ->verifyNow();
    }

    /**
     * @deprecated use toString()
     */
    public function toPrimitive(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function account(): string
    {
        return mb_substr($this->value, 0, mb_strpos($this->value, '@'));
    }

    public function domain(): string
    {
        return mb_substr($this->value, mb_strpos($this->value, '@') + 1);
    }
}
