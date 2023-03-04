<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Address;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;
use mb_convert_case;

final class Location extends ValueObject
{
    private function __construct(
        private string $value
    ) {
        $this->value = mb_convert_case($value, MB_CASE_TITLE, "UTF-8");

        Assert::lazy()->tryAll()
            ->that($value)
            ->string('Value must be a string', 'location.invalidType')
            ->notEmpty('Value must not be empty', 'location.notEmpty')
            ->maxLength(64, 'Value must be 64 characters length', 'location.invalidLength')
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

    public function value(): string
    {
        return $this->toString();
    }
}
