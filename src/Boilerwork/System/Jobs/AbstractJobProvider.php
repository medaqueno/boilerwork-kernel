#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Jobs;

abstract class AbstractJobProvider
{
    public array $jobs = [];
}
