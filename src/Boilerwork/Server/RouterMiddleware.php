#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;


use Boilerwork\Http\Request;
use Boilerwork\Http\Response;
use Boilerwork\Support\Exceptions\CustomException;
use Boilerwork\Support\Singleton;
use FastRoute\RouteCollector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Throwable;

#[Singleton]
final class RouterMiddleware implements MiddlewareInterface
{
    private $dispatcher;

    private static self $instance;

    private static array $routes;

    public static function getInstance($args): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static($args);
        }

        return self::$instance;
    }

    /**
     *
     * @param array $routes
     * @return void
     */
    private function __construct(array $routes)
    {
        foreach ($routes as $route) {
            self::addRoute($route);
        }
    }

    public static function addRoute(array $route)
    {
        self::$routes[] = $route;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach (self::$routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });

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
}
