#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Jobs;

interface JobInterface
{
    public function handle(): void;
}
