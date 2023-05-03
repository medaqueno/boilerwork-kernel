#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes\EventSubscribers;

use App\Shared\Providers\MessagingProvider;
use Attribute;
use Boilerwork\Validation\Assert;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class SubscribesTo
{
    public function __construct(private array $topics, private ?string $target = null)
    {
        Assert::that($topics)
            ->minCount(1, 'Topics parameter value in Attribute SubscribesTo must not be empty');

        foreach ($this->topics as $key => $value) {
            MessagingProvider::addSubscription($value, $target);
        }
    }
}
