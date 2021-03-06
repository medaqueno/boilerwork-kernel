#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain\ValueObjects;

abstract class ValueObject
{
    abstract public function toPrimitive(): mixed;

    abstract public function equals(ValueObject $object): bool;
}
