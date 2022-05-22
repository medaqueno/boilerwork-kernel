#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Jobs;

use App\Shared\Providers\JobsProvider;
use Boilerwork\System\IsProcessInterface;
use Swoole\Process;

final class JobScheduler implements IsProcessInterface
{
    private Process $process;

    private const LOOP_INTERVAL = 5; // Set Maximum each 30 seconds or task set to be repeated each minute may not be executed

    public function __construct(private JobsProvider $jobsProvider)
    {
        $this->process = (new Process(
            function () {
                while (true) {
                    $this->jobsProvider->run();
                    // Use native sleep only with Swoole hooks enabled
                    sleep(self::LOOP_INTERVAL);
                }
            }
        ));
    }

    public function process(): Process
    {
        return $this->process;
    }
}
