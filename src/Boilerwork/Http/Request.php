#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Http;

use Assert\InvalidArgumentException;
use Boilerwork\Authorization\AuthInfo;
use Boilerwork\Persistence\QueryBuilder\PagingDto;
use Boilerwork\Support\ValueObjects\Identity;
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
        parent::__construct(
            serverParams   : $request->getServerParams() ?? [],
            uploadedFiles  : $request->getUploadedFiles() ?? [],
            uri            : $request->getUri(),
            method         : $request->getMethod(),
            body           : 'php://input',
            headers        : $request->getHeaders() ?? [],
            cookies        : $request->getCookieParams() ?? [],
            queryParams    : $request->getQueryParams() ?? [],
            parsedBody     : $this->parseBody($request),
            protocolVersion: '1.1',
        );

        $this->authInfo = $request->getAttribute('AuthInfo');

        $this->paging();
    }

    public function acceptLanguage(): string
    {
        $header = $this->getHeaderLine('x-content-language');

        $language = mb_strtoupper($header);

        return in_array($language, Language::ACCEPTED_LANGUAGES)
            ? $language
            :Language::FALLBACK;
    }

    private function parseBody(ServerRequestInterface $request): array|null|object
    {
        $contentType = $request->getHeaderLine('content-type');
        $method      = $request->getMethod();

        if (in_array($method, ['POST', 'PATCH', 'PUT']) && $contentType === 'application/json') {
            $bodyContents = $request->getBody()->getContents();
            $decodedJson  = json_decode($bodyContents);

            if (json_last_error() === JSON_ERROR_NONE) {
                return (array)$decodedJson;
            }
        }

        return $request->getParsedBody() ?? [];
    }

    public function input(string|int $key): mixed
    {
        return $this->getParsedBody()[$key] ?? null;
    }

    public function query(string|int $param): mixed
    {
        return $this->getQueryParams()[$param] ?? null;
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


    /**
     * Return specific query param
     **/
    public function authInfo(): AuthInfo
    {
        return $this->authInfo;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function tenantId(): Identity
    {
        return Identity::fromString($this->getHeaderLine('x-tenant-id'));
    }
}
