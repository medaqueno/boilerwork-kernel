#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes\Crontab;

use Boilerwork\Attributes\AbstractScanner;

final class CrontabScanner extends AbstractScanner
{
    protected const ATTRIBUTE_CLASS = Crontab::class;

    protected function processAttribute(\ReflectionAttribute $attribute, $parentClass = null): void
    {
        new Crontab(
            interval: $attribute->getArguments()['interval'],
            at: $attribute->getArguments()['at'] ?? null,
            target: $parentClass->getName(),
        );
    }
}
