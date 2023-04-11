#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Tracking;

use Boilerwork\Support\ValueObjects\Identity;
use Zipkin\Annotation;
use Zipkin\Endpoint;
use Zipkin\Propagation\TraceContext;
use Zipkin\Reporters\Http;
use Zipkin\Samplers\BinarySampler;
use Zipkin\Span;
use Zipkin\Tracer;
use Zipkin\TracingBuilder;

use function env;


final class ZipkinBuilder
{
    public readonly Tracer $tracer;
    public readonly Span $initialSpan;

    public function __construct(
        private readonly Identity $transactionId,
        private readonly string $traceId,
        private readonly string $spanId,
        private readonly string $spanName,
        private readonly string $endpointName,
        private readonly ?string $parentId,
        private readonly ?bool $isSampled,
    ) {
        $parentTraceContext = TraceContext::create(
            traceId: $this->traceId,
            spanId: $this->spanId,
            parentId: $this->parentId,
            isSampled: $this->isSampled,
            isDebug: (bool)env('APP_DEBUG'),
        );

        $this->tracer      = $this->createZipkinTracer();
        $this->initialSpan = $this->tracer->nextSpan($parentTraceContext);
        $this->initialSpan->setName($this->spanName);
        $this->initialSpan->tag('transactionId', $transactionId->toString());
    }

    private function createZipkinTracer(): Tracer
    {
        // First we create the endpoint that describes our service
        $endpoint = Endpoint::create($this->endpointName);

        $reporter = new Http(['endpoint_url' => env('ZIPKIN_HOST')]);
        $sampler  = BinarySampler::createAsAlwaysSample();
        $tracing  = TracingBuilder::create()
            ->havingLocalEndpoint($endpoint)
            ->havingSampler($sampler)
            ->havingReporter($reporter)
            ->build();

        return $tracing->getTracer();
    }

    public function toArray(): array
    {
        return [
            'traceId'       => $this->traceId,
            'spanId'        => $this->spanId,
            'spanName'      => $this->spanName,
            'endpointName'  => $this->endpointName,
            'parentId'      => $this->parentId,
            'isSampled'     => $this->isSampled,
        ];
    }

    public function toMessage(): array
    {
        return [
            'traceId'       => $this->traceId,
            'spanId'        => $this->spanId,
            'parentId'      => $this->parentId,
            'isSampled'     => $this->isSampled,
        ];
    }
}
