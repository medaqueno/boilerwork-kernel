#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Messaging;

use OpenSwoole\Process;

final class MessageClient
{
    private Process $process;

    public function __construct(
        private MessagingProviderInterface $subscriptions
    ) {
        $topics = $this->retrieveTopicsToSubscribe();
        echo sprintf('%sActive Topics Subscription:%s%s', PHP_EOL, PHP_EOL, implode(PHP_EOL, $topics));

        $this->process = new Process(callback: function (Process $process) use ($subscriptions, $topics) {
            (new MessageProcessor($subscriptions, $topics))->process();
        }, redirectStdIO: false, enableCoroutine: true);
    }

    private function retrieveTopicsToSubscribe(): array
    {
        /**
         * @var array<topic: string, target: string> $item
         */
        return array_map(function ($item) {
            return sprintf('%s__%s', env('APP_ENV'), $item['topic']);
        }, $this->subscriptions->getSubscriptions());
    }

    public function start(): void
    {
        $this->process->start();
    }
}
