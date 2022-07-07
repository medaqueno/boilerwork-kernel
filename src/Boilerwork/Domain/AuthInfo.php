#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Domain;

use Boilerwork\Domain\ValueObjects\Identity;

final class AuthInfo
{
    public function __construct(
        public readonly Identity $userId,
        public readonly array $permissions,
        public readonly Identity $tenantId,
        public readonly ?string $region,
    ) {
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    public function serialize(): array
    {
        return [
            'userId' => $this->userId->toPrimitive(),
            'permissions' => $this->permissions,
            'tenantId' => $this->tenantId->toPrimitive(),
            'region' => $this->region,
        ];
    }
}
