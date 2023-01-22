#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authorization;

use Boilerwork\Authorization\AuthorizationsProvider;
use Boilerwork\Support\ValueObjects\Identity;
use Psr\Http\Message\ServerRequestInterface;

readonly class AuthInfo
{
    private readonly array $userAuthorizationsParsed;

    private function __construct(
        public readonly Identity $userId,
        public readonly Identity $tenantId,
        public readonly array $authorizations,
    ) {

        // array_filter removes any falsy value that could exist from any non existing authorization string
        $this->userAuthorizationsParsed = array_filter(
            array_map(function ($item) {
                return AuthorizationsProvider::tryFrom($item);
            }, $authorizations)
        );
    }

    public static function fromRequest(
        ServerRequestInterface $request
    ): self {

        $userId = $request->hasHeader('X-Redis-Claim-userId') ? (string)$request->getHeader('X-Redis-Claim-userId') : '';
        $tenantId = $request->hasHeader('X-Redis-Claim-tenantId') ? (string)$request->getHeader('X-Redis-Claim-tenantId') : '';
        $authorizations = $request->hasHeader('X-Redis-Claim-authorizations') ? explode(',', (string)$request->getHeader('X-Redis-Claim-authorizations')) : '';

        if ($userId === '' || $tenantId === '' || $authorizations === '') {
            return new AuthInfoNotFound();
        }

        return new self(
            userId: new Identity($userId),
            tenantId: new Identity($tenantId),
            authorizations: $authorizations,
        );
    }

    /**
     * @param array<userId: string, tenantId: string, authorizations: array> $message
     * @return AuthInfo
     */
    public static function fromMessage(
        array $data
    ): self {

        if (!isset($data['userId']) || !isset($data['tenantId']) || !isset($data['authorizations'])) {
            return new AuthInfoNotFound();
        }

        return new self(
            userId: new Identity($data['userId']),
            tenantId: new Identity($data['tenantId']),
            authorizations: $data['authorizations'],
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
        array_push($allowedAuthorizations, AuthorizationsProvider::MAX_AUTHORIZATION);

        $result = array_filter(
            $allowedAuthorizations,
            function ($item) {
                return in_array($item, $this->userAuthorizationsParsed) || $item === AuthorizationsProvider::PUBLIC;
            }
        );

        return count($result) > 0;
    }

    public function userId(): Identity
    {
        return $this->userId;
    }

    public function tenantId(): Identity
    {
        return $this->tenantId();
    }

    public function authorizations(): array
    {
        return $this->authorizations;
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId->toPrimitive(),
            'tenantId' => $this->tenantId->toPrimitive(),
            'authorizations' => $this->authorizations,
        ];
    }
}
