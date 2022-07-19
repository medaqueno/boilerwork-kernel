#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\AuthInfo;

use Boilerwork\Domain\ValueObjects\Identity;

class AuthInfo
{
    public function __construct(
        public readonly Identity $userId,
        public readonly array $permissions,
        public readonly Identity $tenantId,
        public readonly Identity $transactionId,
        public readonly ?string $region,
    ) {
    }

    public function hasPermission(array $allowedPermissions): bool
    {
        $result = array_filter($allowedPermissions, fn ($item) => in_array($item, $this->permissions) || $item === 'public');

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
