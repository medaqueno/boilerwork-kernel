#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Pools;

use Swoole\Coroutine\Channel;

final class PostgreSQLReadsPool extends PostgreSQLPool
{
    protected ?\Swoole\Coroutine\Channel $pool = null;

    public function __construct()
    {

        var_dump(__CLASS__);
    }

    public function initPool(string $host, int $port, string $dbname, string $username, string $password, int $connectionSize = 1,  string $applicationName = 'AppService'): void
    {
        echo "\n\nIntenta entrar READS \n\n";

        if ($this->pool !== null) {
            return;
        }

        $this->pool = new Channel((int)$connectionSize);
        echo "\n\nPOSTGRES READS POOL CONSTRUCTOR: " . $this->pool->capacity . "\n\n";

        for ($i = 0; $i < $connectionSize; $i++) {
            $this->putConn(
                $this->createConnection($host, $port, $dbname, $username, $password, $applicationName)
            );
        }

        echo sprintf("\nPostgres Pool created: %s.%s - %s connections opened\n", $host, $dbname,  $this->pool->capacity);
    }
}
