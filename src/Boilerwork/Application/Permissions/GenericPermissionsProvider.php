#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Application\Permissions;

/**
 * Generic permissions used by all Services
 */
enum GenericPermissionsProvider: string
{
    case CAN_OPERATE_ALL = 'CanOperateAll';
    case PUBLIC = 'Public';

    case CAN_ACCESS_WHOLESAVER = 'CanAccessWholesaver';
    case CAN_ACCESS_RETAILER = 'CanAccessRetailer';
}
