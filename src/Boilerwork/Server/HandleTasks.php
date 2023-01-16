#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

use OpenSwoole\Server;

/**
 *
 **/
final class HandleTasks
{
    public function onTask(Server $server, int $taskId, int $fromId, mixed $data): void
    {
        swoole_set_process_name('swoole_task_' . $taskId);
        echo  '\nTask start swoole_task_' . $taskId;
    }
}
