#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Traits;

trait Transactions
{
    final public function initTransaction(): void
    {
        if ($this->conn === null) $this->conn = $this->sqlConnector->getConn();

        // Execute at the end of coroutine process
        \Swoole\Coroutine\defer(function () {
            echo "\nDEFER\n\n";
            $this->sqlConnector->putConn($this->conn);
        });

        $this->conn->query('BEGIN');
    }

    final public function endTransaction(): void
    {
        $this->conn->query('COMMIT');
    }
}
