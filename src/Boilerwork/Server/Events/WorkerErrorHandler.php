#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server\Events;

use OpenSwoole\Server;

class WorkerErrorHandler
{
    public function __invoke(Server $server, int $workerId,): void
    {
        echo sprintf('Error in Worker %d', $workerId);
        echo PHP_EOL;
    }
}
