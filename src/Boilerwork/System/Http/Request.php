#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Http;

use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleRequest;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\System\AuthInfo\AuthInfo;
use Boilerwork\System\AuthInfo\AuthInfoNotFound;

/**
 * Implements Laminas Diactoros PSR-7 and PSR-17, Psr\Http\Message\ServerRequestInterface
 * https://docs.laminas.dev/laminas-diactoros/v2/overview/
 **/
class Request extends ServerRequest implements ServerRequestInterface
{
    /**
     * Builds Psr\Http\Message\ServerRequestInterface
     * with extra methods
     **/
    public function __construct(SwooleRequest $swooleRequest)
    {
        parent::__construct(
            serverParams: $swooleRequest->server ?? [],
            uploadedFiles: $swooleRequest->files ?? [],
            uri: $swooleRequest->server['request_uri'],
            method: $swooleRequest->server['request_method'],
            body: 'php://input',
            headers: $swooleRequest->header ?? [],
            cookies: $swooleRequest->cookie ?? [],
            queryParams: $swooleRequest->get ?? [],
            parsedBody: $this->parseBody($swooleRequest),
            protocol: '1.1'
        );

        $this->setAuthInfo();
    }

    private function parseBody(SwooleRequest $request): array
    {
        $body = [];
        if (
            ($request->server['request_method'] === 'POST'
                || $request->server['request_method'] === 'PATCH'
                || $request->server['request_method'] === 'PUT'
            )
            && $request->header['content-type'] === 'application/json'
        ) {
            $body = $request->rawContent();
            $body = empty($body) ? [] : json_decode($body);
        } else {
            $body = $request->post ?? [];
        }

        return (array)$body;
    }

    /**
     * Return specific input received in body or post
     **/
    public function input(string|int $key): mixed
    {
        return $this->getParsedBody()[$key] ?? null;
    }

    /**
     * Adds AuthInfo in the Container
     **/
    public function setAuthInfo(): void
    {
        container()->instance('AuthInfo', $this->getAuthInfo());
    }

    /**
     * Return user metadata relative.
     **/
    public function getAuthInfo(): AuthInfo
    {
        try {
            $response =  new AuthInfo(
                userId: new Identity($this->getHeaderLine('userId')),
                permissions: explode(',', $this->getHeaderLine('permissions')),
                tenantId: new Identity($this->getHeaderLine('tenantId')),
                transactionId: $this->getHeaderLine('transactionId') ?: Identity::create(),
                region: $this->getHeaderLine('region') ?: null,
            );
        } catch (\Exception $e) {
            $response = new AuthInfoNotFound();
        }

        return $response;
    }
}
