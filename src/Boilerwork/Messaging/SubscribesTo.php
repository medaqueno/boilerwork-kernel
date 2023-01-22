#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Messaging;

use Attribute;
use Boilerwork\Validation\Assert;

#[Attribute(Attribute::TARGET_CLASS, Attribute::IS_REPEATABLE)]
final readonly class SubscribesTo
{
    private MessageClientInterface $messageClient;

    public function __construct(private array $topics = [])
    {
        $this->messageClient = globalContainer()->get(MessageClientInterface::class);
        Assert::that($topics)->minCount(1, 'Topics parameter value in Attribute SubscribesTo must not be empty');
    }

    public function __invoke(string $subscriber)
    {
        /*
        echo "\n SubscribesTos: " . $subscriber . "  \n";
        foreach ($this->topics as $key => $value) {
            // var_dump($value);
            // $this->messageClient->subscribe();
        }
        */
    }
}
