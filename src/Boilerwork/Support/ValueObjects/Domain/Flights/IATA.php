#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Domain\Flights;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Validation\Assert;

/**
 *
 **/
final class IATA extends ValueObject
{
    private string $iataCodePattern = '/^[A-z]{3}$/';

    private readonly string $code;

    private function __construct(
        string $code
    ) {
        Assert::lazy()->tryAll()
            ->that($code)
            ->regex($this->iataCodePattern, 'Value must be a IATA code', 'IATACode.invalidFormat')
            ->verifyNow();

        $this->code = strtoupper($code);
    }

    public static function fromString(string $code): self
    {
        return new self(code: $code);
    }

    public function toString(): string
    {
        return $this->code;
    }
}