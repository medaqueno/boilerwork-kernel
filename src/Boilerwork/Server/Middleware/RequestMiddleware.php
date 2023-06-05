#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server\Middleware;

use Boilerwork\Http\Request;
use Boilerwork\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestMiddleware implements MiddlewareInterface
{
    private static self $instance;

    public static function getInstance(RouterInterface $router): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static($router);
        }

        return self::$instance;
    }

    private function __construct(private RouterInterface $router)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routerResponse = $this->router->retrieveHandler($request);

        if ($routerResponse instanceof ResponseInterface) {
            return $routerResponse;
        }

        $response = $this->handleRequest($request, $routerResponse);

        // Collect garbage
        \OpenSwoole\Coroutine::defer(function () {
            $this->collectGarbage();
        });

        return $response;
    }

    private function handleRequest(ServerRequestInterface $request, array $routerResponse): ResponseInterface
    {
        $request = new Request($request);

        foreach ($routerResponse['vars'] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        $handler = globalContainer()->get($routerResponse['handler']);

        return $handler($request, $routerResponse['vars']);
    }

    private function collectGarbage(): void
    {
        $memory_limit = ini_get('memory_limit');

        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            if ($matches[2] == 'G') {
                $memory_limit = $matches[1] * 1024 * 1024 * 1024;  // GB a bytes
            } else if ($matches[2] == 'M') {
                $memory_limit = $matches[1] * 1024 * 1024; // MB a bytes
            } else if ($matches[2] == 'K') {
                $memory_limit = $matches[1] * 1024; // KB a bytes
            }
        }

        $umbral = $memory_limit * 0.15;  // 0.65 = 65% del límite de memoria

        if (memory_get_usage() > $umbral) {
//            echo 'FORCE GARBAGE COLLECTION\n\n';
//            logger("FORCE GARBAGE COLLECTION");
            gc_collect_cycles();
        }

    }
}
