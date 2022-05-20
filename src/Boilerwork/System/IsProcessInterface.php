#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System;

use Swoole\Process;

interface IsProcessInterface
{
    public function process(): Process;
}
