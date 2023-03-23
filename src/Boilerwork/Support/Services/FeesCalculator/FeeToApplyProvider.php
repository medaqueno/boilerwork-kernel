#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator;

enum FeeToApplyProvider: string
{
    case SINGLE = 'single_service';
    case COMBINED = 'combined_service';
    case TRANSPORT_ACCOMODATION = 'transport_accommodation';
}
