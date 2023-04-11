#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Tracking;

use Boilerwork\Authorization\AuthInfo;
use Boilerwork\Messaging\Message;
use Boilerwork\Support\ValueObjects\Identity;
use Psr\Http\Message\ServerRequestInterface;
use Zipkin\Annotation;

use function env;
use function sprintf;
use function var_dump;


final class TrackingContext
{
    public const NAME = 'trackingContext';

    private AuthInfo $authInfo;

    private function __construct(
        private readonly Identity $transactionId,
        public readonly mixed $trazability,
    ) {
    }

    public static function fromRequest(
        Identity $transactionId,
        ServerRequestInterface $request,
    ): self {

        $input = $request->getUri()->getPath();
        $pattern = '/(?<!-)\//'; // Coincide con las barras inclinadas que no están precedidas o seguidas por un guión
        $replacement = '-';
        $output = ltrim(preg_replace($pattern, $replacement, $input), '-');

        $trazability = new ZipkinBuilder(
            transactionId: $transactionId,
            traceId: $request->getHeaderLine('X-B3-Traceid'),
            spanId: $request->getHeaderLine('X-B3-Spanid'),
            spanName: sprintf('%s-%s-%s', env('APP_NAME'), $output, $request->getMethod()),
            endpointName: env('APP_NAME'),
            parentId: $request->getHeaderLine('X-B3-Parentspanid'),
            isSampled: (bool)$request->getHeaderLine('X-B3-Sampled'),
        );

        $instance = new self(
            transactionId: $transactionId,
            trazability: $trazability
        );

        return $instance;
    }

    public static function fromMessage(
        string $transactionId,
        Message $message,
    ): self {
        $transactionId = Identity::fromString($transactionId);

        $trazability = new ZipkinBuilder(
            transactionId: $transactionId,
            traceId: 'X-B3-Traceid',
            spanId: 'X-B3-Spanid',
            spanName: sprintf('%s-%s-%s-%s', env('APP_NAME'), 'message', 'consume', $message->topic),
            endpointName: env('APP_NAME'),
            parentId: 'X-B3-Parentspanid',
            isSampled: (bool) 'X-B3-Sampled',
        );

        $instance = new self(
            transactionId: $transactionId,
            trazability: $trazability
        );

        return $instance;
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
            'transactionId' => $this->transactionId->toString(),
            'authInfo'      => $this->authInfo?->toArray(),
            'trazability'      => $this->trazability?->toMessage(),
        ];
    }

    public function serialize(): string
    {
        return json_encode($this->toArray());
    }
}
