#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes\EventSubscribers;

use App\Shared\Providers\MessagingProvider;
use Attribute;
use Boilerwork\Attributes\AbstractScanner;
use Boilerwork\Validation\Assert;

final class SubscribesToScanner extends AbstractScanner
{
    protected const ATTRIBUTE_CLASS = SubscribesTo::class;

    protected function processAttribute(\ReflectionAttribute $attribute, $parentClass = null): void
    {
        new SubscribesTo(
            topics: $attribute->getArguments()['topics'],
            target: $parentClass->getName(),
        );
    }
}
