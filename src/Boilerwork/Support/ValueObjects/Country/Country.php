#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects\Country;

use Boilerwork\Foundation\ValueObjects\ValueObject;

/**
 *  Creates UUID using Symfony\Polyfill implementation, which turns out to be faster than pecl extension.
 **/
final class Country extends ValueObject
{
    public function __construct(
        protected Iso31661Alpha2Code $iso31661Alpha2Code,
        protected Iso31661Alpha3Code $iso31661Alpha3Code,
        protected string $englishName,
    ) {
    }

    public static function fromIso31661Alpha2Code(Iso31661Alpha2Code $code): static
    {
        $data = DataProvider::fromIso31661Alpha2Code($code);

        $alpha2      = new Iso31661Alpha2Code($data[0]);
        $alpha3      = new Iso31661Alpha3Code($data[1]);

        $name        = $data[3];

        return new static($alpha2, $alpha3, $name);
    }

    public static function fromIso31661Alpha3Code(Iso31661Alpha3Code $code): static
    {
        $data = DataProvider::fromIso31661Alpha3Code($code);

        $alpha2      = new Iso31661Alpha2Code($data[0]);
        $alpha3      = new Iso31661Alpha3Code($data[1]);
        $name        = $data[3];

        return new static($alpha2, $alpha3, $name);
    }

    public function iso31661Alpha2Code(): Iso31661Alpha2Code
    {
        return $this->iso31661Alpha2Code;
    }

    public function iso31661Alpha3Code(): Iso31661Alpha3Code
    {
        return $this->iso31661Alpha3Code;
    }

    public function englishName(): string
    {
        return $this->englishName;
    }

    public function equals(ValueObject $object): bool
    {
        return $this->iso31661Alpha2Code->toPrimitive() === $object->iso31661Alpha2Code->toPrimitive()
            && $this->iso31661Alpha3Code->toPrimitive() === $object->iso31661Alpha3Code->toPrimitive()
            && $this->englishName === $object->englishName
            && $object instanceof self;
    }

    public function toPrimitive(): string
    {
        return $this->iso31661Alpha2Code()->toPrimitive();
    }

    public function toArray(): array
    {
        return [
            'iso31661Alpha2Code' => $this->iso31661Alpha2Code()->toPrimitive(),
            'iso31661Alpha3Code' => $this->iso31661Alpha3Code()->toPrimitive(),
            'englishName' => $this->englishName(),
        ];
    }
}
