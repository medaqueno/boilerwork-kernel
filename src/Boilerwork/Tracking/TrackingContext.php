#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Tracking;

use Boilerwork\Authorization\AuthInfo;
use Boilerwork\Support\ValueObjects\Identity;
use Zipkin\Annotation;


final class TrackingContext
{
    public const NAME = 'trackingContext';

    private AuthInfo $authInfo;
    private ?ZipkinBuilder $trazability = null;

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
        $transactionId = Identity::fromString($transactionId);

        return new self(
            transactionId: $transactionId,
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

    public function trazability(): ?ZipkinBuilder
    {
        return $this->trazability;
    }

    public function addTrazability(?ZipkinBuilder $trazability): void
    {
        $this->trazability = $trazability;
    }

    public function toArray(): array
    {
        return [
            'transactionId' => $this->transactionId->toString(),
            'authInfo' => $this->authInfo?->toArray(),
            'trazability' => $this->trazability !== null ? $this->trazability?->toMessage() : null,
        ];
    }

    public function serialize(): string
    {
        return json_encode($this->toArray());
    }
}
