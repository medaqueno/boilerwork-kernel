#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authorization;

use Boilerwork\Support\ValueObjects\Identity;
use Boilerwork\Validation\Assert;

final class AuthIdentity extends Identity
{
    public function __construct(
        protected string $value
    ) {
        Assert::lazy()->tryAll()
            ->that($value)
            ->notEmpty('Value received by AuthInfo must not be empty', 'authInfoUuid.notEmpty')
            ->uuid('Value received by AuthInfo must be a valid UUID', 'authInfoUuid.invalidFormat')
            ->verifyNow();

        parent::__construct($value);
    }

    /**
     * Create new Identity from String
     **/
    public static function fromString(string $uuid): static
    {
        return new static($uuid);
    }
}
