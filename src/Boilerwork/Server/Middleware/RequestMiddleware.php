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
        if (! isset(self::$instance)) {
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

        return $this->handleRequest($request, $routerResponse);
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
}
