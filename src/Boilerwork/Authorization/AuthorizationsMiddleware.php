#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authorization;

use Boilerwork\Http\Response;
use Boilerwork\Tracking\TrackContextNotFoundException;
use Boilerwork\Tracking\TrackingContext;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthorizationsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly array $routes
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var TrackingContext $trackingContext
         */
        $trackingContext = container()->has(TrackingContext::NAME) ? container()->get(TrackingContext::NAME) : throw new TrackContextNotFoundException();

        $authInfo = AuthInfo::fromRequest($request);

        $trackingContext->addAuthInfo($authInfo);

        $hasAuthorization = $this->hasAuthorization(
            authInfo: $authInfo,
            method: $request->getMethod(),
            uri: $request->getUri()->getPath()
        );

        return $hasAuthorization === true ? $handler->handle($request) : Response::error(new AuthorizationException());
    }

    private function hasAuthorization(AuthInfo $authInfo, string $method, string $uri): bool
    {
        $response = false;
        foreach ($this->routes as $item) {

            if (isset($item[3]) && $item[0] === $method && $this->matchUrl(pattern: $item[1], url: $uri)) {
                $response = $authInfo->hasAuthorization($item[3]);
                break;
            }
        }

        return $response;
    }


    private function matchUrl($pattern, $url): bool
    {
        if (strpos($url, $pattern) === 0) {
            return true;
        } else {
            $regex = preg_replace("#\{(.*)\}#", "(.*)", $pattern);
            if (preg_match("#^" . $regex . "$#", $url)) {
                return true;
            }
        }

        return false;
    }
}