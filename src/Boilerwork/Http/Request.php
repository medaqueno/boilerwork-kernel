#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Http;

use Boilerwork\Authorization\AuthInfo;
use Boilerwork\Persistence\QueryBuilder\PagingDto;
use Boilerwork\Support\ValueObjects\Language\Iso6391Code;
use Boilerwork\Support\ValueObjects\Language\Language;
use Psr\Http\Message\ServerRequestInterface;
use OpenSwoole\Core\Psr\ServerRequest as OpenSwooleRequest;

/**
 * Implements PSR-7 and PSR-17, Psr\Http\Message\ServerRequestInterface
 **/
class Request extends OpenSwooleRequest implements ServerRequestInterface
{
    private readonly AuthInfo $authInfo;

    /**
     * Builds Psr\Http\Message\ServerRequestInterface
     * with extra methods
     **/
    public function __construct(ServerRequestInterface $request)
    {

        $server = $request->getServerParams();
        $headers = $request->getHeaders();

        parent::__construct(
            serverParams: $request->getServerParams() ?? [],
            uploadedFiles: $request->getUploadedFiles() ?? [],
            uri: $server['request_uri'],
            method: $server['request_method'],
            body: 'php://input',
            headers: $headers ?? [],
            cookies: $request->getCookieParams() ?? [],
            queryParams: $request->getQueryParams() ?? [],
            parsedBody: $this->parseBody($request),
            protocolVersion: '1.1'
        );

        $this->authInfo = $request->getAttribute('AuthInfo');

        $this->paging();
    }

    public function acceptLanguage(): string
    {
        $headers = $this->getHeaders();
        $langRequest = (string)$headers['accept-language'];

        return  isset($langRequest) ?
            mb_strtoupper(Language::fromIso6391Code(new Iso6391Code($langRequest))->toPrimitive())
            : Language::FALLBACK;
    }

    private function paging(): void
    {
        if (!isset($this->getQueryParams()['per_page']) || !isset($this->getQueryParams()['page'])) {
            return;
        }

        new PagingDto(
            perPage: (int)$this->getQueryParams()['per_page'],
            page: (int)$this->getQueryParams()['page']
        );
    }

    private function parseBody(ServerRequestInterface $request): array
    {
        $server = $request->getServerParams();
        $headers = $request->getHeaders();
        $body = [];
        if (
            ($server['request_method'] === 'POST'
                || $server['request_method'] === 'PATCH'
                || $server['request_method'] === 'PUT'
            )
            && $headers['content-type'] === 'application/json'
        ) {
            $body = $request->getBody()->getContents();
            $body = empty($body) ? [] : json_decode($body);
        } else {
            $body = $request->getParsedBody() ?? [];
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
     * Return specific query param
     **/
    public function authInfo(): AuthInfo
    {
        return $this->authInfo;
    }
}
