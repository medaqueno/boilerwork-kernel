#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

/**
 *
 **/
final class HandleTcpUdp
{
    public function onReceive(\Swoole\Server $server, int $fd, int $reactorId, string $data): void
    {
        echo "\nonReceive\n";
    }
}
