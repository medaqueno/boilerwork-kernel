#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain\Specifications;

class AnyOfSpecification extends Specification
{
    private readonly array $specifications;

    public function __construct(array ...$specifications)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($object): bool
    {
        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($object)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function whereExpression(string $alias): string
    {
        return implode(' AND ', array_map(
            function (Specification $specification) use ($alias) {
                return '(' . $specification->whereExpression($alias) . ')';
            },
            $this->specifications
        ));
    }
}
