#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authorization;

/**
 * Generic permissions used by all Services
 */
enum GenericPermissionsProvider: string
{
    case CAN_MANAGE_ALL = 'CanManageAll';
    case PUBLIC = 'Public';

    case CAN_ACCESS_WHOLESAVER = 'CanAccessWholesaver';
    case CAN_ACCESS_RETAILER = 'CanAccessRetailer';
}
