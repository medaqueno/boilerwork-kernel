#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Authorization;

use Boilerwork\Authentication\AuthInfo\AuthInfo;
use Boilerwork\Authentication\AuthInfo\AuthInfoNotFound;
use Boilerwork\Authentication\AuthInfo\Exceptions\AuthorizationException;
use Boilerwork\Http\Response;
use Boilerwork\Support\ValueObjects\Identity;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthorizationsMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly array $routes)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authInfo = $this->authInfo($request);

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
            if (isset($item[3]) && $item[0] === $method && $item[1] === $uri) {
                $response = $authInfo->hasAuthorization($item[3]);
                break;
            }
        }

        return $response;
    }

    /**
     * Return user metadata relative.
     **/
    public function authInfo(ServerRequestInterface $request): AuthInfo
    {
        try {
            $response = AuthInfo::fromRequest(
                userId: new Identity($request->getHeaderLine('X-Redis-Claim-userId')),
                tenantId: new Identity($request->getHeaderLine('X-Redis-Claim-tenantId')),
                authorizations: explode(',', $request->getHeaderLine('X-Redis-Claim-authorizations')),
                //     // transactionId: $this->getHeaderLine('transactionId') ?: Identity::create(),
                //     // region: $this->getHeaderLine('region') ?: null,
            );
        } catch (\Exception $e) {
            $response = new AuthInfoNotFound();
        }

        return $response;
    }
}
