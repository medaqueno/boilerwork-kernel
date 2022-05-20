#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Jobs;

interface JobInterface
{
    public function handle(): void;
}
