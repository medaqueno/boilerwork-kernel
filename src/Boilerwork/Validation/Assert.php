#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Validation;

use Assert\Assert as BaseAssertion;

class Assert extends BaseAssertion
{
    protected static $lazyAssertionExceptionClass = '\Boilerwork\Validation\CustomAssertionFailedException';
    // protected static $exceptionClass = '\Boilerwork\Domain\Exceptions\CustomAssertionFailedException';
}
