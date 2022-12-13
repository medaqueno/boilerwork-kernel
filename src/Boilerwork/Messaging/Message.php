#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Messaging;

use Boilerwork\Authentication\AuthInfo\AuthInfo;
use Boilerwork\Authentication\AuthInfo\AuthInfoNotFound;
use Boilerwork\Authentication\AuthInfo\HasAuthInfo;
use Boilerwork\Support\ValueObjects\Identity;
use DateTimeInterface;

final class Message
{
    use HasAuthInfo;

    public function __construct(
        public readonly string $payload,
        public readonly string $topic,
        public readonly DateTimeInterface $createdAt,
        public readonly mixed $error,
        public readonly ?string $key,
        public readonly array $headers,
    ) {
        $this->setAuthInfo();
    }

    public function parsedPayload()
    {
        return json_decode($this->payload);
    }

    /**
     * Return user metadata relative.
     **/
    public function authInfo(): AuthInfo
    {
        $payload = $this->parsedPayload();
        try {
            $userId = $payload->metadata->userId ?? 'b6699fce-244d-41d3-a6f5-708417455548'; // Only if payload or metadata attributes are absent. TODO: This MUST BE CHANGED
            $tenantId = $payload->metadata->tenantId ?? 'ec643eed-b299-4ff1-8dbf-6fe08ed92b25'; // Only if payload or metadata attributes are absent. TODO: This MUST BE CHANGED

            $response =  AuthInfo::fromRequest(
                userId: new Identity($userId),
                tenantId: new Identity($tenantId),
                authorizations: $payload->metadata->authorizations ?? [], // Only if payload or metadata attributes are absent. TODO: This MUST BE CHANGED
                // transactionId: isset($payload->metadata->transactionId) ? new Identity($payload->metadata->transactionId) : Identity::create(),
                // region: 'eu',
            );
        } catch (\Exception $e) {
            $response = new AuthInfoNotFound();
        }

        return $response;
    }
}
