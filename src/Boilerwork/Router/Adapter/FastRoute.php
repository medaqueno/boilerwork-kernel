#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Router\Adapter;

use Boilerwork\Http\Response;
use Boilerwork\Router\RouterInterface;
use Exception;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function FastRoute\simpleDispatcher;

class FastRoute implements RouterInterface
{
    private $dispatcher;

    private static array $routes = [];

    public function __construct(
        array $routes,
    ) {
        foreach ($routes as $route) {
            self::addRoute(
                $route[0],
                $route[1],
                $route[2],
                $route[3],
            );
        }
    }

    public static function addRoute(string $method, string $route, string $handler, array $authorizations): void
    {
        self::$routes[] = [
            'method'  => $method,
            'route'   => $route,
            'handler' => $handler,
            'auth'    => $authorizations,
        ];
    }

    public function getRoutes(): array
    {
        return self::$routes;
    }

    public function retrieveHandler(ServerRequestInterface $request): mixed
    {
        $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->getRoutes() as $route) {
                $r->addRoute($route['method'], $route['route'], $route['handler']);
            }
        });

        return $this->handleRequest($request);
    }

    private function handleRequest(ServerRequestInterface $request): ResponseInterface|array
    {
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        if (count($routeInfo) === 1) {
            return Response::empty(404);
        }

        $code    = $routeInfo[0];
        $handler = $routeInfo[1];
        $vars    = empty($routeInfo[2]) ? [] : $routeInfo[2];

        switch ($code) {
            case Dispatcher::NOT_FOUND:
                return Response::empty(404);
            case Dispatcher::METHOD_NOT_ALLOWED:
                return Response::empty(405);
            case Dispatcher::FOUND:
                return ['handler' => $handler, 'vars' => $vars, 'code' => $code];
            default:
                throw new Exception(
                    message: "Server error. Contact system administrator",
                    code   : 500,
                );
        }
    }
}
