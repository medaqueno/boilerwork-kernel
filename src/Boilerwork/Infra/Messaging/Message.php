#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Messaging;

use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\System\AuthInfo\AuthInfo;
use Boilerwork\System\AuthInfo\AuthInfoNotFound;
use Boilerwork\System\AuthInfo\HasAuthInfo;
use DateTimeInterface;

final class Message implements HasAuthInfo
{
    public function __construct(
        public readonly string $payload,
        public readonly string $topic,
        public readonly DateTimeInterface $createdAt,
        public readonly mixed $error,
        public readonly ?string $key,
        public readonly array $headers,
    ) {
        // $this->setAuthInfo();
    }

    public function parsedPayload()
    {
        return json_decode($this->payload);
    }

    /**
     * Adds AuthInfo in the Container
     **/
    public function setAuthInfo(): void
    {
        container()->instance('AuthInfo', $this->authInfo());
    }

    /**
     * Return user metadata relative.
     **/
    public function authInfo(): AuthInfo
    {
        $payload = $this->parsedPayload();
        try {
            $response =  new AuthInfo(
                userId: new Identity($payload->metadata->userId),
                permissions: $payload->metadata->permissions,
                tenantId: new Identity($payload->metadata->tenantId),
                transactionId: isset($payload->metadata->transactionId) ? new Identity($payload->metadata->transactionId) : Identity::create(),
                region: $payload->metadata->region,
            );
        } catch (\Exception $e) {
            $response = new AuthInfoNotFound();
        }

        return $response;
    }
}
