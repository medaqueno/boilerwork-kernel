#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server\Events;

use OpenSwoole\Server;
use OpenSwoole\Timer;

class WorkerExitHandler
{
    public function __invoke(Server $server, int $workerId,): void
    {
        echo sprintf('Exit Worker %d', $workerId);
        echo PHP_EOL;
        Timer::clearAll();
    }
}
