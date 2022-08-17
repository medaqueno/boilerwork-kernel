#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\AuthInfo;

use Boilerwork\Domain\ValueObjects\Identity;

class AuthInfo
{
    public function __construct(
        public readonly Identity $userId,
        public readonly array $permissions = [],
        public readonly Identity $tenantId,
        public readonly Identity $transactionId,
        public readonly ?string $region,
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
            'userId' => $this->userId->toPrimitive(),
            'permissions' => $this->permissions,
            'tenantId' => $this->tenantId->toPrimitive(),
            'transactionId' => $this->transactionId->toPrimitive(),
            'region' => $this->region,
        ];
    }
}
