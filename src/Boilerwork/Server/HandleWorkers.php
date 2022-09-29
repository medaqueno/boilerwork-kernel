#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

/**
 *
 **/
final class HandleWorkers
{
    public function onWorkerStart(\Swoole\Server $server, int $workerId): void
    {
        swoole_set_process_name('swoole_worker_' . $workerId);
        // echo "\nWorker start swoole_worker_" . $workerId;
    }

    public function onWorkerStop(\Swoole\Server $server, int $workerId): void
    {
        echo "\nWorker Stop " . $workerId, "\n";
    }

    public function onWorkerError(\Swoole\Server $server, int $workerId): void
    {
        echo "\nWorker Error " . $workerId, "\n";
        error('############### Worker Error: ' . $workerId);
        // $server->stop($workerId);
        // $server->finish(true);
        // $server->shutdown();
    }
}
