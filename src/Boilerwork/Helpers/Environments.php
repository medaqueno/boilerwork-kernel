<?php

declare(strict_types=1);

namespace Boilerwork\Helpers;

enum Environments: string
{
    case DEVELOPMENT = 'dev';
    case TEST = 'test';
    case QA = 'qa';
    case PREPRODUCTION = 'pre';
    case PRODUCTION = 'production';
}
