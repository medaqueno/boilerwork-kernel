#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes\Routing;

use Boilerwork\Attributes\AbstractScanner;

final class RouteScanner extends AbstractScanner
{
    protected const ATTRIBUTE_CLASS = Route::class;

    protected function processAttribute(\ReflectionAttribute $attribute, $parentClass = null): void
    {
        new Route(
            method: $attribute->getArguments()['method'],
            route: $attribute->getArguments()['route'],
            authorizations: $attribute->getArguments()['authorizations'],
            target: $parentClass->getName(),
        );
    }
}