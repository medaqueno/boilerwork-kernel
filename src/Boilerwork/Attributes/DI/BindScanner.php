#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes\DI;

use App\Shared\Providers\MessagingProvider;
use Attribute;
use Boilerwork\Attributes\AbstractScanner;
use Boilerwork\Attributes\Routing\Route;
use Boilerwork\Validation\Assert;

final class BindScanner extends AbstractScanner
{
    protected const ATTRIBUTE_CLASS = Bind::class;

    protected function processAttribute(\ReflectionAttribute $attribute, $parentClass = null): void
    {
        new Bind(
            abstract: $attribute->getArguments()['abstract'],
            concrete: $attribute->getArguments()['concrete']
        );
    }
}
