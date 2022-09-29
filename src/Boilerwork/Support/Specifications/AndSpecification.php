#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Specifications;

class AndSpecification extends Specification
{
    public function __construct(
        public readonly Specification $one,
        public readonly Specification $other
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($object): bool
    {
        return $this->one->isSatisfiedBy($object) && $this->other->isSatisfiedBy($object);
    }

    /**
     * {@inheritdoc}
     */
    public function whereExpression(string $alias): string
    {
        return sprintf(
            sprintf(
                '(%s) AND (%s)',
                $this->one->whereExpression($alias),
                $this->other->whereExpression($alias)
            )
        );
    }
}
