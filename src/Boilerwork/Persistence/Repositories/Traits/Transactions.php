#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Traits;

trait Transactions
{
    final public function initTransaction(): void
    {
        $this->sqlConnector->conn->query('BEGIN');
    }

    final public function endTransaction(): void
    {
        $this->sqlConnector->conn->query('COMMIT');
    }
}
