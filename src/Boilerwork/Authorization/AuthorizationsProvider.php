#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authorization;

/**
 * Generic authorization used by all Services
 */
enum AuthorizationsProvider: string
{
    case MAX_AUTHORIZATION = 'CanManageAll';
    case TENANT_ADMIN = 'TenantAdmin';
    case TENANT_USER = 'TenantUser';
    case PUBLIC = 'Public';
}
