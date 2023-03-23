#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\FeesCalculator;

interface FeesCalculatorData
{
    static public function dataProvider(
        string $serviceType,
        string $idTenant,
        ?string $idCart = null,
        ?array $servicesInCart = [],
    ): FeesCalculatorDataProvider;
}
