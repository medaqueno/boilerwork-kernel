#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Tracking;

use Boilerwork\Container\IsolatedContainer;
use Boilerwork\Support\ValueObjects\Identity;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function env;
use function error;
use function ltrim;
use function preg_replace;
use function sprintf;

final class TrackingMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Create an isolated container for each Request
        globalContainer()->setIsolatedContainer(new IsolatedContainer);

        $transactionId = Identity::create();
        $trackingContext = TrackingContext::fromRequest(
            transactionId: $transactionId,
        );

        try {
            $trackingContext->addTrazability(
                $this->buildTrazability($request, $transactionId)
            );
        } catch (\Exception $e) {
            error('Error Building trazability', $e->getMessage());
        }


        // Make it Accesible in local isolated container
        container()->instance(TrackingContext::NAME, $trackingContext);

        return $handler->handle($request);
    }

    private function buildTrazability(ServerRequestInterface $request, Identity $transactionId): ZipkinBuilder
    {
        $input = $request->getUri()->getPath();
        $pattern = '/(?<!-)\//'; // Coincide con las barras inclinadas que no están precedidas o seguidas por un guión
        $replacement = '-';
        $output = ltrim(preg_replace($pattern, $replacement, $input), '-');

        return new ZipkinBuilder(
            transactionId: $transactionId,
            traceId: $request->getHeaderLine('X-B3-Traceid'),
            spanId: $request->getHeaderLine('X-B3-Spanid'),
            spanName: sprintf('%s-%s', $output, $request->getMethod()),
            endpointName: env('APP_NAME'),
            parentId: $request->getHeaderLine('X-B3-Parentspanid'),
            isSampled: (bool)$request->getHeaderLine('X-B3-Sampled'),
        );
    }
}
