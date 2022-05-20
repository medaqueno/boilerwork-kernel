#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

use Assert\Assert as BaseAssertion;

class Assert extends BaseAssertion
{
    protected static $lazyAssertionExceptionClass = '\Boilerwork\Domain\CustomAssertionFailedException';
    // protected static $exceptionClass = '\Boilerwork\Domain\CustomAssertionFailedException';
}
