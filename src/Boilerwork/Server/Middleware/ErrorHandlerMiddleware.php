#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server\Middleware;

use Boilerwork\Server\ExceptionHandler;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private ExceptionHandler $exceptionHandler;

    public function __construct()
    {
        $this->exceptionHandler = new ExceptionHandler();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Exception $exception) {
            return $this->exceptionHandler->handle($exception);
        }
    }
}
