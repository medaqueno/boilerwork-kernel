#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain\ValueObjects\Country;

use Boilerwork\Domain\ValueObjects\ValueObject;
use Boilerwork\Domain\Assert;

/**
 * @internal Creates UUID using Symfony\Polyfill implementation, which turns out to be faster than pecl extension.
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
        return true;
        // return $this->iso31661Alpha2Code->sameValueAs($object->iso31661Alpha2Code()) && $this->iso31661Alpha3Code->sameValueAs($object->iso31661Alpha3Code()) && $this->englishName->sameValueAs($object->englishName()) && $this->phoneNumberPrefix->sameValueAs($object->phoneNumberPrefix);
    }

    public function toPrimitive(): string
    {
        return $this->iso31661Alpha2Code()->toPrimitive();
    }
}
