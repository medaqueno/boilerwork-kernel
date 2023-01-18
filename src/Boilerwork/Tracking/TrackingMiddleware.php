#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Tracking;

use Boilerwork\Container\IsolatedContainer;
use Boilerwork\Support\ValueObjects\Identity;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class TrackingMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Create an isolated container for each Request
        globalContainer()->setIsolatedContainer(new IsolatedContainer);

        $trackingContext = TrackingContext::fromRequest(
            transactionId: Identity::create()
        );

        // Make it Accesible in local isolated container
        container()->instance(TrackingContext::NAME, $trackingContext);

        return $handler->handle($request);
    }
}
