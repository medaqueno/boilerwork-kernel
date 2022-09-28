#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Http;

use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleRequest;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Persistence\QueryBuilder\Paging;
use Boilerwork\System\AuthInfo\AuthInfo;
use Boilerwork\System\AuthInfo\AuthInfoNotFound;
use Boilerwork\System\AuthInfo\HasAuthInfo;

/**
 * Implements Laminas Diactoros PSR-7 and PSR-17, Psr\Http\Message\ServerRequestInterface
 * https://docs.laminas.dev/laminas-diactoros/v2/overview/
 **/
class Request extends ServerRequest implements ServerRequestInterface
{
    use HasAuthInfo;

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
        $this->paging();
    }

    private function paging(): void
    {
        if (!isset($this->getQueryParams()['perPage']) || !isset($this->getQueryParams()['page'])) {
            return;
        }

        new Paging(
            perPage: (int)$this->getQueryParams()['perPage'],
            page: (int)$this->getQueryParams()['page']
        );
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
     * Return specific query param
     **/
    public function query(string|int $param): mixed
    {
        return $this->getQueryParams()[$param] ?? null;
    }

    /**
     * Return user metadata relative.
     **/
    public function authInfo(): AuthInfo
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
