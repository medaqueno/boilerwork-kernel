#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Http;

use Boilerwork\Container\IsolatedContainer;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use OpenSwoole\Core\Psr\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouterMiddleware implements MiddlewareInterface
{
    private $dispatcher;

    private array $routes;
    private RouteCollector $r;

    public function __construct()
    {
        $routesPath = base_path('/routes/httpApi.php');
        $this->dispatcher = \FastRoute\simpleDispatcher(
            function (\FastRoute\RouteCollector $r) use ($routesPath) {
                $routes = include($routesPath);
                $this->routes = $routes;
                // var_dump($routes);
                foreach ($routes as $route) {
                    $r->addRoute($route[0], $route[1], $route[2]);
                }

                $this->r = $r;
            }
        );
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $isolatedContainer = new IsolatedContainer;
        globalContainer()->setIsolatedContainer($isolatedContainer);

        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        if (count($routeInfo) === 1) {
            return new Response('Not found', 404, '', ['Content-Type' => 'text/plain']);
        }

        $code = $routeInfo[0];
        $handler = $routeInfo[1];
        $vars = empty($routeInfo[2]) ? [] : $routeInfo[2];

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                return new Response('Not found', 404, '', ['Content-Type' => 'text/plain']);
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                return new Response('Method not allowed', 405, '', ['Content-Type' => 'text/plain']);
            case \FastRoute\Dispatcher::FOUND:
                foreach ($routeInfo[2] as $key => $value) {
                    $request = $request->withAttribute($key, $value);
                }
                echo "\n REQUEST \n";
                var_dump($request);


                $result = (globalContainer()->get($handler))($request, $vars);
                echo "\nRESULT\n";
                var_dump($result);
                return $routeInfo[1]($request);
        }
    }

    public function getRouteCollector(): RouteCollector
    {
        return $this->r;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
