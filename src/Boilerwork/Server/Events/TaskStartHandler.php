#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server\Events;

use OpenSwoole\Server;

class TaskStartHandler
{
    public function __invoke(Server $server, $task_id, $reactorId, $data): void
    {
        echo sprintf('Task %d started on server PID: %d', $task_id, $server->getMasterPid());
        echo PHP_EOL;
    }
}
