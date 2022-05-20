#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Jobs;

use App\Shared\Providers\JobProvider;
use Kernel\System\IsProcessInterface;
use Swoole\Process;

final class JobScheduler implements IsProcessInterface
{
    private Process $process;

    private const LOOP_INTERVAL = 5; // Set Maximum each 30 seconds or task set to be repeated each minute may not be executed

    public function __construct(private JobProvider $jobProvider)
    {
        $this->process = (new Process(
            function () {
                while (true) {
                    $this->jobProvider->run();
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
