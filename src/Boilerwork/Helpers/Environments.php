<?php

declare(strict_types=1);

namespace Boilerwork\Helpers;

enum Environments: string
{
    case DEVELOPMENT = 'DEV';
    case TEST = 'TEST';
    case QA = 'QA';
    case PREPRODUCTION = 'PRE';
    case PRODUCTION = 'PROD';
}
