#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server\Events;

use OpenSwoole\Server;
use OpenSwoole\Util;

class ServerStartHandler
{
    public function __invoke(Server $server): void
    {
        Util::setProcessName('openswoole_server');
        echo PHP_EOL . PHP_EOL;
        echo sprintf(
            "SERVER STARTED: %s v%s at %s:%s",
            get_class($server),
            Util::getVersion(),
            env('SERVER_IP'),
            env('SERVER_PORT'),
        );
        echo PHP_EOL . PHP_EOL;
    }
}
