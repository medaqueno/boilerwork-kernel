<?php

/**
 * This file is part of the ValueObject package.
 *
 * (c) Lorenzo Marzullo <marzullo.lorenzo@gmail.com>
 */

// namespace Boilerwork\Support\ValueObjects\Country;
namespace Boilerwork\Support\ValueObjects\Country;

use Boilerwork\Foundation\ValueObjects\ValueObject;

/**
 * Class Iso31661Alpha3Code.
 *
 * @package ValueObject
 * @author  Lorenzo Marzullo <marzullo.lorenzo@gmail.com>
 * @link    http://github.com/lorenzomar/valueobject
 *
 */
class Iso31661NumericCode extends ValueObject
{

    public function __construct(
        public readonly string $value
    ) {
    }

    public function toPrimitive(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $object): bool
    {
        return $object instanceof self && $this->value === $object->toPrimitive();
    }
}
