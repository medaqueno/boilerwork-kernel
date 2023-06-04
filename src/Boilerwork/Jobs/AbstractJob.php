#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Jobs;

abstract class AbstractJob
{
    abstract public function handle(): void;
}
