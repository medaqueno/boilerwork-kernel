#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Traits;

trait Transactions
{
    final public function initTransaction(): void
    {
        // $this->conn->query('BEGIN');
    }

    final public function endTransaction(): void
    {
        // $this->conn->query('COMMIT');
    }
}
