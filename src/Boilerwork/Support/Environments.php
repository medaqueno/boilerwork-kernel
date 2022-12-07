<?php

declare(strict_types=1);

namespace Boilerwork\Support;

enum Environments: string
{
    case LOCAL = 'LOCAL';
    case DEVELOPMENT = 'DEV';
    case TEST = 'TEST';
    case QA = 'QA';
    case PREPRODUCTION = 'PRE';
    case PRODUCTION = 'PROD';
}
