#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Specifications;

class NotSpecification extends Specification
{
    public function __construct(private readonly Specification $specification)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($object): bool
    {
        return !$this->specification->isSatisfiedBy($object);
    }
}
