#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

use OpenSwoole\Process;

interface IsProcessInterface
{
    public function process(): Process;
}
