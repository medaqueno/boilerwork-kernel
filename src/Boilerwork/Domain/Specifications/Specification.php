#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain\Specifications;

use BadMethodCallException;

/**
 * Specification Pattern
 *
 * @example
 *  $paid = new PaidOrderSpecification;
 *  $unshipped = new UnshippedOrderSpecification;
 *  $cancelled = new CancelledOrderSpecification;
 *
 *  $paid->and($unshipped)->isSatisfiedBy(new Order); // => true
 *
 *  (new OneOfSpecification($paid, $unshipped, $cancelled))->isSatisfiedBy(new Order); // => true
 *
 *
 * Based on https://github.com/tanigami/specification-php and https://github.com/dddinphp/ddd
 * **/

abstract class Specification
{
    abstract public function isSatisfiedBy(mixed $value): bool;

    public function whereExpression(string $alias): string
    {
        throw new BadMethodCallException('Where expression is not supported');
    }

    public function and(Specification $specification): AndSpecification
    {
        return new AndSpecification($this, $specification);
    }

    public function or(Specification $specification): OrSpecification
    {
        return new OrSpecification($this, $specification);
    }

    public function not(): NotSpecification
    {
        return new NotSpecification($this);
    }

    public function oneOf(): OneOfSpecification
    {
        return new OneOfSpecification($this);
    }

    public function noneOf(): NoneOfSpecification
    {
        return new NoneOfSpecification($this);
    }

    public function anyOf(): AnyOfSpecification
    {
        return new AnyOfSpecification($this);
    }
}
