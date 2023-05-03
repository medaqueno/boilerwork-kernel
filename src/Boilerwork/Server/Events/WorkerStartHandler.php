#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server\Events;

use OpenSwoole\Server;

class WorkerStartHandler
{
    public function __invoke(Server $server, int $workerId): void
    {
        echo sprintf('Worker %d started on server PID: %d', $workerId, $server->getMasterPid());
        echo PHP_EOL;
    }
}
