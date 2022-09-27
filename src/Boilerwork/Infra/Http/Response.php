#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Infra\Http;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Implements Laminas Diactoros PSR-7 and PSR-17
 * https://docs.laminas.dev/laminas-diactoros/v2/overview/
 **/
class Response
{
    public static function json(mixed $data = '', int $status = 200, array $headers = []): ResponseInterface
    {
        return (new JsonResponse(
            self::wrapResponse($data),
            $status,
            $headers
        ));
    }

    public static function text(string|StreamInterface $text = '', int $status = 200, array $headers = []): ResponseInterface
    {
        $text = $text instanceof StreamInterface ? $text->getContents() : $text;

        return (new TextResponse(
            self::wrapResponse($text),
            $status,
            $headers
        ));
    }

    public static function empty(int $status = 204, array $headers = []): ResponseInterface
    {
        return (new EmptyResponse(
            $status,
            $headers
        ));
    }

    private static function wrapResponse(mixed $data): array
    {
        return [
            'metadata' => self::addMetaData(),
            'data' => $data,
        ];
    }

    private static function addMetaData()
    {
        if (!container()->has('Paging')) {
            return;
        }

        $pagingContainer = container()->get('Paging');

        return [
            'perPage' => $pagingContainer->perPage(),
            'page' => $pagingContainer->page(),
            'totalCount' => $pagingContainer->totalCount(),
        ];
    }
}
