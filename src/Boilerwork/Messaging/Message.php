#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Messaging;

use Boilerwork\Authorization\AuthInfo;
use Boilerwork\Support\ValueObjects\Identity;
use Boilerwork\Tracking\TrackingContext;
use Boilerwork\Tracking\ZipkinBuilder;
use DateTimeInterface;
use function env;
use function sprintf;

final class Message
{
    public function __construct(
        public readonly string            $payload,
        public readonly string            $topic,
        public readonly DateTimeInterface $createdAt,
        public readonly mixed             $error,
        public readonly ?string           $key,
        public readonly array             $headers,
    ) {
    }

    public function parsedPayload()
    {
        return json_decode($this->payload);
    }

    public function trackingContext(): TrackingContext
    {
        $trazability = null;

        if (isset($this->parsedPayload()->metadata->trackingContext)) {
            $transactionId = $this->parsedPayload()->metadata->trackingContext->transactionId;
            $authInfo = (array)$this->parsedPayload()->metadata->trackingContext->authInfo;

            if ($this->parsedPayload()->metadata->trackingContext?->trazability !== null) {
                $trazability = new ZipkinBuilder(
                    transactionId: Identity::fromString($transactionId),
                    traceId: $this->parsedPayload()->metadata->trackingContext->trazability?->traceId ?? Identity::create()->toString(),
                    spanId: $this->parsedPayload()->metadata->trackingContext->trazability?->spanId ?? Identity::create()->toString(),
                    spanName: sprintf('%s-%s-%s', 'message', 'consume', $this->topic),
                    endpointName: sprintf('%s', env('APP_NAME')),
                    parentId: $this->parsedPayload()->metadata->trackingContext->trazability->parentId ?? null,
                    isSampled: (bool)$this->parsedPayload()->metadata->trackingContext->trazability->isSampled ?? null,
                );
            }
        } else {
            $transactionId = Identity::create()->toString();
            $authInfo = [];
        }

        $trackingContext = TrackingContext::fromMessage($transactionId);

        if ($trazability !== null) {
            $trackingContext->addTrazability($trazability);
        }


        $trackingContext->addAuthInfo(AuthInfo::fromMessage($authInfo));

        return $trackingContext;
    }
}
