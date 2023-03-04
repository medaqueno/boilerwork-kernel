<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Address;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;

final class Street extends ValueObject
{
    private function __construct(
        private string $name,
        private ?int $number = null,
        private ?string $other1 = null,
        private ?string $other2 = null
    ) {
        Assert::lazy()->tryAll()
            ->that($name)
            ->string('Name must be a string', 'street.invalidType')
            ->notEmpty('Name must not be empty', 'street.name.notEmpty')
            ->verifyNow();
    }

    public static function fromValues(
        string $name,
        ?int $number = null,
        ?string $other1 = null,
        ?string $other2 = null
    ): self {
        return new self(
            name: $name,
            number: $number,
            other1: $other1,
            other2: $other2
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function number(): ?int
    {
        return $this->number;
    }

    public function other1(): ?string
    {
        return $this->other1;
    }

    public function other2(): ?string
    {
        return $this->other2;
    }

    public function value(): array
    {
        return [
            'name' => $this->name(),
            'number' => $this->number(),
            'other1' => $this->other1(),
            'other2' => $this->other2()
        ];
    }
}
