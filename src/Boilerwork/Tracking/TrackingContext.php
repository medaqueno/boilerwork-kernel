#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Tracking;

use Boilerwork\Authorization\AuthInfo;
use Boilerwork\Support\ValueObjects\Identity;

final class TrackingContext
{
    public const NAME = 'trackingContext';

    private AuthInfo $authInfo;

    private function __construct(
        private readonly Identity $transactionId,
    ) {
    }

    public static function fromRequest(
        Identity $transactionId,
    ): self {

        return new self(
            transactionId: $transactionId,
        );
    }

    public static function fromMessage(
        string $transactionId,
    ): self {
        return new self(
            transactionId: Identity::fromString($transactionId),
        );
    }

    public function transactionId(): Identity
    {
        return $this->transactionId;
    }

    public function authInfo(): ?AuthInfo
    {
        return $this->authInfo;
    }

    public function addAuthInfo(?AuthInfo $authInfo): void
    {
        $this->authInfo = $authInfo;
    }

    public function toArray(): array
    {
        return [
            'transactionId' => $this->transactionId->toPrimitive(),
            'authInfo' => $this->authInfo?->toArray(),
        ];
    }

    public function serialize(): string
    {
        return json_encode($this->toArray());
    }
}
