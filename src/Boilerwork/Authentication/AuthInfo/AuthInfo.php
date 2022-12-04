#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authentication\AuthInfo;

use Boilerwork\Support\ValueObjects\Identity;

class AuthInfo
{
    public function __construct(
        public readonly Identity $userId,
        public readonly Identity $tenantId,
        public readonly Identity $transactionId,
        public readonly ?string $region,
        public readonly array $permissions = [],
    ) {
    }

    /**
     * Check if User has permissions needed in the permissions provided.
     *
     * CanManageAll permission is checked automatically.
     *
     */
    public function hasPermission(array $allowedPermissions): bool
    {
        // Add Permission by default
        array_push($allowedPermissions, 'CanManageAll');

        $result = array_filter(
            $allowedPermissions,
            function ($item) {
                return in_array($item, $this->permissions) || $item === 'Public';
            }
        );

        return count($result) > 0;
    }

    public function serialize(): array
    {
        return [
            // 'userId' => $this->userId->toPrimitive(),
            // 'permissions' => $this->permissions,
            // 'tenantId' => $this->tenantId->toPrimitive(),
            // 'transactionId' => $this->transactionId->toPrimitive(),
            // 'region' => $this->region,
            'userId' => 'D015DDD9-4687-4191-B976-DE1696D6AFE3',
            'permissions' => $this->permissions,
            'tenantId' => '5789F9AF-BE4C-4CD0-9B4B-16A05CE26BF3',
            'transactionId' => '846D7E70-2F3F-49D1-ACE1-BF6A63454388',
            'region' => $this->region,
        ];
    }
}
