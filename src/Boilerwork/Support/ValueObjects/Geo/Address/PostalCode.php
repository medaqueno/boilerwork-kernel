<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Geo\Address;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;

class PostalCode extends ValueObject
{
    private function __construct(
        private readonly string $value,
    ) {
        Assert::lazy()->tryAll()
            ->that($value)
            ->string('Value must be a string', 'postalCode.invalidType')
            ->notEmpty('Value must not be empty', 'postalCode.notEmpty')
            ->maxLength(24, 'Value must be 24 characters length', 'location.invalidLength')
            ->verifyNow();
    }

    public static function fromString(string $value): self
    {
        return new self(value: $value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
