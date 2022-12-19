#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Language;

use Boilerwork\Validation\Assert;
use Boilerwork\Foundation\ValueObjects\ValueObject;

final class Language extends ValueObject
{
    private function __construct(
        protected readonly Iso6391Code $iso6391
    ) {
    }

    public static function fromString(string $alpha2): static
    {
        return new static(new Iso6391Code($alpha2));
    }

    public static function fromIsoCode(string $code): static
    {
        return self::fromString($code);
    }

    public static function fromIso6391Code(Iso6391Code $code): static
    {
        return new static($code);
    }

    public function iso6391(): Iso6391Code
    {
        return $this->iso6391;
    }

    public function isoCode(): string
    {
        return $this->iso6391()->toPrimitive();
    }

    public function equals(ValueObject $object): bool
    {
        return $this->toPrimitive() === $object->toPrimitive()
            && $object instanceof self;
    }

    public function toPrimitive(): string
    {
        return $this->iso6391()->toPrimitive();
    }

    public function name(): string
    {
        return LanguageDataProvider::data()[$this->toPrimitive()];
    }

    public function toArray(): array
    {
        return [
            'iso' => $this->isoCode()
        ];
    }

    public static function allValues(): array
    {
        return array_map(
            fn ($k) => self::fromIsoCode($k),
            array_keys(LanguageDataProvider::data())
        );
    }
}
