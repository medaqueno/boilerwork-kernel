#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authorization;

/**
 * Generic authorization used by all Services
 */
enum AuthorizationsProvider: string
{
    case PUBLIC = 'public';

    case IS_SUPER_ADMIN = 'is_super_admin';

        // Permissions by user type
    case IS_ADMIN_QUADRANT = 'is_admin_quadrant';
    case IS_ADMIN_TENANT = 'is_admin_tenant';
    case IS_USER_TENANT = 'is_user_tenant';

        // Permissions by interface
    case UI_ADVISER = 'ui_adviser';
    case UI_MANAGER = 'ui_manager';

        // Permissions by action
    case DASHBOARD = 'dashboard';
    case CATALOGUE = 'catalogue';
    case ANALYTICS = 'analytics';
    case OWN_PRODUCT = 'own_product';
    case BOOKING_INVOICING = 'booking_invoicing';
    case USERS = 'users';
    case TENANTS = 'tenants';
    case MASTERS = 'masters';
    case INTEGRATIONS = 'integrations';
    case FEES = 'fees';
    case MY_TENANT = 'my_tenant';

    public static function getIsAdminQuadrant(): array
    {
        return [
            self::IS_ADMIN_QUADRANT->value,
            self::UI_ADVISER->value,
            self::UI_MANAGER->value,
            self::DASHBOARD->value,
            self::CATALOGUE->value,
            self::ANALYTICS->value,
            self::OWN_PRODUCT->value,
            self::BOOKING_INVOICING->value,
            self::USERS->value,
            self::TENANTS->value,
            self::MASTERS->value,
            self::INTEGRATIONS->value,
            self::FEES->value,
            self::MY_TENANT->value,
        ];
    }

    public static function getIsAdminTenant(): array
    {
        return [
            self::IS_ADMIN_TENANT->value,
            self::UI_ADVISER->value,
            self::UI_MANAGER->value,
            self::DASHBOARD->value,
            self::CATALOGUE->value,
            self::ANALYTICS->value,
            self::OWN_PRODUCT->value,
            self::BOOKING_INVOICING->value,
            self::MY_TENANT->value,
            self::INTEGRATIONS->value,
            self::FEES->value,

        ];
    }

    public static function getIsUserTenant(): array
    {
        return [
            self::IS_USER_TENANT->value,
            self::UI_ADVISER->value,
            self::UI_MANAGER->value,
            self::DASHBOARD->value,
            self::ANALYTICS->value,
            self::OWN_PRODUCT->value,
            self::BOOKING_INVOICING->value,
            self::INTEGRATIONS->value,
            self::FEES->value,
        ];
    }

    public static function getIsUiAdviser(): array
    {
        return [
            self::IS_SUPER_ADMIN->value,
            self::IS_ADMIN_TENANT->value,
            self::IS_USER_TENANT->value,
            self::UI_ADVISER->value
        ];
    }

    public static function getIsUiManager(): array
    {
        return [
            self::IS_SUPER_ADMIN->value,
            self::IS_ADMIN_TENANT->value,
            self::IS_USER_TENANT->value,
            self::UI_MANAGER->value,
            //self::DASHBOARD->value,
            self::CATALOGUE->value,
            self::ANALYTICS->value,
            self::OWN_PRODUCT->value,
            self::BOOKING_INVOICING->value,
            self::USERS->value,
            self::TENANTS->value,
            self::MASTERS->value,
            self::INTEGRATIONS->value,
            self::FEES->value,
            self::MY_TENANT->value,
        ];
    }

    public static function isValid(string $value): bool
    {
        return is_null(self::tryFrom($value));
    }
}
