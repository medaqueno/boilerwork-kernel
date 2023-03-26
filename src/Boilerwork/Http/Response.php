#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Http;

use Boilerwork\Support\Exceptions\CustomException;
use Boilerwork\Validation\CustomAssertionFailedException;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;

/**
 * Implements Laminas Diactoros PSR-7 and PSR-17
 * https://docs.laminas.dev/laminas-diactoros/v2/overview/
 **/
final class Response
{
    private array $customMetadata = [];

    private function __construct(
        private mixed $data = '',
        private int $status = 200,
        private array $headers = []
    ) {
    }

    /**
     * Create a configurable Response Object
     * which can be used later
     */
    public static function create(string|array $data = '', int $status = 200, array $headers = []): self
    {
        return new self(data: $data, status: $status, headers: $headers);
    }

    /**
     * Add metadata to current metadata array
     */
    public function addMetadata(array $customMetadata): self
    {
        $this->customMetadata = array_merge($this->customMetadata, $customMetadata);
        return $this;
    }

    /**
     * Return current metadata
     */
    public function metadata(): array
    {
        return $this->customMetadata;
    }

    /**
     * Empty current metadata.
     */
    public function resetMetadata(): self
    {
        $this->customMetadata = [];
        return $this;
    }

    /**
     * Add header to current headers array
     */
    public function addHeader(string $key, string $value): self
    {
        $this->headers = array_merge($this->headers, [$key => $value]);
        return $this;
    }

    /**
     * Return current headers
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Empty current headere.
     */
    public function resetHeaders(): self
    {
        $this->headers = [];
        return $this;
    }

    /**
     * Set Http Status Code
     */
    public function setHttpStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Transform to ResponseInterface with JSON Format
     */
    public function toJson(): ResponseInterface
    {
        return new JsonResponse(
            $this->wrapResponse($this->data),
            $this->status,
            $this->headers
        );
    }

    /**
     * Create a ResponseInterface with JSON Format directly
     */
    public static function json(mixed $data = '', int $status = 200, array $headers = []): ResponseInterface
    {
        return (new self(data: $data, status: $status, headers: $headers))->toJson();
    }

    /**
     * Transform to ResponseInterface with text Format
     */
    public function toText(): ResponseInterface
    {
        if (is_string($this->data) === false) {
            throw new \InvalidArgumentException('Only string can be used with toText method');
        }

        $text = $this->data instanceof StreamInterface ? $this->data->getContents() : $this->data;

        return (new TextResponse(
            $text,
            $this->status,
            $this->headers
        ));
    }

    /**
     * Create a ResponseInterface with text Format directly
     */
    public static function text(string|StreamInterface $data = '', int $status = 200, array $headers = []): ResponseInterface
    {
        return (new self(data: $data, status: $status, headers: $headers))->toText();
    }

    /**
     * Transform to ResponseInterface with no content
     */
    public function toEmpty(): ResponseInterface
    {
        return (new EmptyResponse(
            status: $this->status,
            headers: $this->headers
        ));
    }

    /**
     * Create a ResponseInterface with no content directly
     */
    public static function empty(int $status = 204, array $headers = []): ResponseInterface
    {
        return (new self(status: $status, headers: $headers))->toEmpty();
    }

    private function wrapResponse(mixed $data): array
    {
        $metaData = array_merge($this->addPagination(), $this->customMetadata);

        return [
            'metadata' => $metaData,
            'data' => $data,
        ];
    }

    private function addPagination(): array
    {
        if (!container()->has('Paging')) {
            return [];
        }

        $pagingContainer = container()->get('Paging');

        return $pagingContainer->serialize();
    }

    public static function error(Throwable $th, ?ServerRequestInterface $request = null): ResponseInterface
    {
        if ($th instanceof CustomAssertionFailedException || $th instanceof \Assert\InvalidArgumentException) {
            $status = 422;
            $code =  "validationError";
            $message = "Request is invalid or malformed";
            $errors = json_decode($th->getMessage());
        } else if ($th instanceof CustomException) {
            $parse = json_decode($th->getMessage());

            $status = $th->getCode();
            $code =  $parse->error->code;
            $message =  $parse->error->message;
            $errors = [];
        } else {
            $status  = $th->getCode() >= 100 && $th->getCode() <= 599 ? $th->getCode() : 500;
            $code =  "serverError";
            $message = $th->getMessage() ?: 'Server error.';
            $errors = [];
        }

        $result = [
            "error" =>
            [
                "code" => $code,
                "message" => $message,
                "errors" => $errors
            ]
        ];

        if (env('APP_DEBUG') === 'true') {
            $result['error']['dev'] = [
                "message" =>  $th->getMessage(),
                "file" => $th->getFile(),
                "line" => $th->getLine(),
                "request" => $request,
                "trace" => env('TRACE_ERRORS') === "true" ? $th->getTrace() : null,
            ];
        }

        return new JsonResponse(
            data: $result,
            status: $status,
        );
    }
}
