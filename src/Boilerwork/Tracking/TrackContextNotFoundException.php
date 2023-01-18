#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Tracking;

use Boilerwork\Support\Exceptions\CustomException;

class TrackContextNotFoundException extends CustomException
{
    public function __construct()
    {
        parent::__construct('trackContext.notFound', 'Tracking Context Information was not found', 403);
    }
}
