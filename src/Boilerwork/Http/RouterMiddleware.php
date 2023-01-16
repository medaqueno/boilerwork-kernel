#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Http;

use Boilerwork\Container\IsolatedContainer;
use Boilerwork\Http\Response;
use Boilerwork\Support\Exceptions\CustomException;
use FastRoute\RouteCollector;
use OpenSwoole\Core\Psr\Response as PsrResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Throwable;

final class RouterMiddleware implements MiddlewareInterface
{
    private $dispatcher;

    private RouteCollector $r;

    public function __construct()
    {
        $routesPath = base_path('/routes/httpApi.php');

        $this->dispatcher = \FastRoute\simpleDispatcher(
            function (\FastRoute\RouteCollector $r) use ($routesPath) {
                $routes = include($routesPath);
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

        $request = new Request($request);
        try {
            return $this->handleRequest($request);
        } catch (\Throwable $th) {
            return $this->handleErrors($th, $request);
        }
    }

    private function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        if (count($routeInfo) === 1) {
            return Response::empty(404);
        }

        $code = $routeInfo[0];
        $handler = $routeInfo[1];
        $vars = empty($routeInfo[2]) ? [] : $routeInfo[2];

        switch ($code) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                return Response::empty(404);
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                return Response::empty(405);
            case \FastRoute\Dispatcher::FOUND:

                // $this->checkAuthorization(uri: $request_uri, method: $request_method);

                foreach ($routeInfo[2] as $key => $value) {
                    $request = $request->withAttribute($key, $value);
                }

                $handler = globalContainer()->get($handler);

                if (is_callable($handler) === false) {
                    throw new RuntimeException('Port must be callable');
                }

                return $handler($request, $vars);
            default:

                $exception = new CustomException(
                    code: "serverError",
                    message: "Server error. Contact system administrator",
                    httpStatus: 500
                );

                return Response::error(
                    th: $exception,
                    request: $request
                );
        }
    }

    private function handleErrors(Throwable $th, ServerRequestInterface $request): ResponseInterface
    {
        error($th);

        echo sprintf("\n ERROR HANDLED:: %s \n", $th->getMessage() ?: "No error message found");

        return Response::error(
            th: $th,
            request: $request
        );
    }

    public function getRouteCollector(): RouteCollector
    {
        return $this->r;
    }

    private function checkAuthorization($uri, $method): void
    {
        // foreach ($this->getRoutes() as $item) {
        //     if (isset($item[3]) && $item[0] === $method && $item[1] === $uri) {

        //         authInfo()->hasAuthorization($item[3]) === true ?: throw new AuthorizationException();

        //         break;
        //     }
        // }
    }
}
