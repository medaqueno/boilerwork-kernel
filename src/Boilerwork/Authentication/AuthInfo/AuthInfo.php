#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authentication\AuthInfo;

use Boilerwork\Authorization\AuthorizationsProvider;
use Boilerwork\Support\ValueObjects\Identity;

class AuthInfo
{
    private function __construct(
        public readonly Identity $userId,
        public readonly Identity $tenantId,
        public readonly array $authorizations,
    ) {
    }

    public static function fromRequest(
        Identity $userId,
        Identity $tenantId,
        array $authorizations = [],
    ): self {
        return new self(
            userId: $userId,
            tenantId: $tenantId,
            authorizations: $authorizations,
            // transactionId: $transactionId,
            // region: $region,
        );
    }

    /**
     * Check if User has authorizations needed in the authorizations provided.
     *
     * AuthorizationsProvider::MAX_AUTHORIZATION authorization is added to allowed authorization automatically.
     * If the endpoint has Public authorization, it will pass.
     *
     */
    public function hasAuthorization(array $allowedAuthorizations): bool
    {
        // Add Max permission by default to allowed Authorizations
        array_push($allowedAuthorizations, AuthorizationsProvider::MAX_AUTHORIZATION->value);

        $result = array_filter(
            $allowedAuthorizations,
            function ($item) {
                return in_array($item, $this->authorizations) || $item === AuthorizationsProvider::PUBLIC->value;
            }
        );

        return count($result) > 0;
    }

    public function serialize(): array
    {
        return [
            'userId' => $this->userId->toPrimitive(),
            'tenantId' => $this->tenantId->toPrimitive(),
            'authorizations' => $this->authorizations,
            // 'transactionId' => $this->transactionId->toPrimitive(),
            // 'region' => $this->region,
            // 'userId' => 'D015DDD9-4687-4191-B976-DE1696D6AFE3',
            // 'authorizations' => $this->authorizations,
            // 'tenantId' => '5789F9AF-BE4C-4CD0-9B4B-16A05CE26BF3',
            // 'transactionId' => '846D7E70-2F3F-49D1-ACE1-BF6A63454388',
            // 'region' => $this->region,
        ];
    }
}
