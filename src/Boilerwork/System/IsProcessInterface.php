#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System;

use Swoole\Process;

interface IsProcessInterface
{
    public function process(): Process;
}
