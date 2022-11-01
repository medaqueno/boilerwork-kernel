#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

use Boilerwork\Authentication\AuthInfo\AuthInfoNotFound;
use Boilerwork\Container\IsolatedContainer;
use Boilerwork\Http\Request;
use Boilerwork\Http\Response;
use Boilerwork\Validation\CustomAssertionFailedException;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Runtime;

/**
 *
 **/
final class HandleHttp
{
    private \FastRoute\Dispatcher $httpDispatcher;
    private \FastRoute\RouteCollector $r;
    private array $routes;

    public function __construct(string $routesPath)
    {
        // Init Routing
        $this->httpDispatcher = $this->initRouting($routesPath);
    }

    public function getRouteCollector(): \FastRoute\RouteCollector
    {
        return $this->r;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    private function initRouting(string $routesPath): \FastRoute\Dispatcher
    {
        // Init Routing
        return \FastRoute\simpleDispatcher(
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

    public function onRequest(SwooleRequest $request, SwooleResponse $response): void
    {
        $response->setHeader('Content-Type', 'application/json');

        // Rate Limit -> It should be a middleware
        /*
        $Ratelimiter = RateLimiter::getInstance();
        $count = $Ratelimiter->access($request->server['remote_addr']);
        if ($count > $Ratelimiter::MAX_REQUESTS) {
            $response->setStatusCode(429);
            $response->header("Content-Type", "text/plain");
            $response->end("Blocked");
            return;
        }
        */



        try {
            $result = $this->handleRequest($request);

            foreach ($result->getHeaders() as $key => $value) {
                $response->setHeader($key, $value[0]);
            }

            $response->setStatusCode($result->getStatusCode(), $result->getReasonPhrase());
            $result = $result->getBody()->__toString();
        } catch (\Throwable $e) {

            // error($e);

            if ($e instanceof CustomAssertionFailedException || $e instanceof \Assert\InvalidArgumentException) {
                var_dump($e->getMessage());
                $response->setStatusCode(422);
                $result = [
                    "error" =>
                    [
                        "code" => "validationError",
                        "message" => "Request is invalid or malformed",
                        "errors" => json_decode($e->getMessage())
                    ]
                ];
            } else if ($e instanceof AuthInfoNotFound) {
                // var_dump($e->getErrorExceptions());
                $response->setStatusCode($e->getCode());
                $result = [
                    "error" =>
                    [
                        "code" => "authInfoError",
                        "message" => $e->getMessage(),
                        "errors" => []
                    ]
                ];
            } else {

                go(function () use ($e, $request) {

                    if (class_exists(\Sentry\SentrySdk::class)) {
                        Runtime::setHookFlags(0);
                        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($request): void {
                            $scope->setTag('environment', env('APP_ENV'));
                            $scope->setTag('qt.method', $request->getMethod());
                            $scope->setTag('qt.endpoint', $request->server['request_uri']);
                            $scope->setContext(
                                'Request',
                                [
                                    'queryString' => $request->server['query_string'] ?? 'none',
                                    'content' => $request->getContent(),
                                    'headers' => $request->header,
                                ]
                            );
                        });

                        \Sentry\captureException($e);
                        Runtime::setHookFlags(\SWOOLE_HOOK_ALL);
                    }
                });


                // // https://jsonapi.org/examples/#error-objects
                $code = $e->getCode() >= 500 ? $e->getCode() : 500;
                $response->setStatusCode($code);
                $result = [
                    "error" =>
                    [
                        "code" => "serverError",
                        "message" => $e->getMessage(),
                        "errors" => []
                    ]
                ];

                if (env('APP_DEBUG') === 'true') {
                    array_push($result['error']['errors'], [
                        "message" =>  $e->getMessage(),
                        "file" => $e->getFile(),
                        "line" => $e->getLine(),
                        "trace" => env('TRACE_ERRORS') === "true" ? $e->getTrace() : null,
                    ]);
                }
            }

            $result = json_encode($result, \JSON_PRETTY_PRINT);
        }

        $response->end($result);

        // go(function () {
        //     getMemoryStatus();
        // });
    }

    private function handleRequest(SwooleRequest $swooleRequest): ResponseInterface
    {
        $isolatedContainer = new IsolatedContainer;
        globalContainer()->setIsolatedContainer($isolatedContainer);

        // Convert Swoole Request to Psr\Http\Message\ServerRequestInterface
        // $request = HttpRequest::createFromSwoole($swooleRequest);
        $request = new Request($swooleRequest);

        $request_method = $request->getMethod();
        $request_uri = $request->getServerParams()['request_uri'];

        $dispatched = $this->httpDispatcher->dispatch($request_method, $request_uri);

        if (count($dispatched) === 1) {
            return Response::empty(404);
        }

        $code = $dispatched[0];
        $handler = $dispatched[1];
        $vars = empty($dispatched[2]) ? [] : $dispatched[2];

        switch ($code) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $result = Response::empty(404);
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $result = Response::empty(405);
                break;
            case \FastRoute\Dispatcher::FOUND:

                // $this->checkAuthorization(uri: $request_uri, method: $request_method);
                // $this->checkMiddlewares(request: $request, uri: $request_uri, method: $request_method);


                if (is_array($handler)) {
                    // Custom method in class
                    $className = $handler[0];
                    $method = $handler[1];
                    $class = globalContainer()->get($className);
                    $result = $class->$method($request, $vars);
                } else {
                    // invokable class  __invoke
                    $result = (globalContainer()->get($handler))($request, $vars);
                    // $result = (new $handler)($request, $vars);
                }

                break;
            default:
                $data = [
                    "error" =>
                    [
                        "code" => "serverError",
                        "message" => "Server error. Contact system administrator",
                        "errors" => []
                    ]
                ];

                $result = Response::json($data, 500);
        }

        return $result;
    }

    private function checkAuthorization($uri, $method): void
    {
        foreach ($this->getRoutes() as $item) {
            if (isset($item[3]) && $item[0] === $method && $item[1] === $uri) {

                authInfo()->hasPermission($item[3]) === true ?: throw new \Exception("User has not permission", 403);
                break;
            }
        }
    }

    private function checkMiddlewares(Request $request, $uri, $method): void
    {
        foreach ($this->getRoutes() as $item) {
            if (isset($item[4]) && count($item[4]) > 0 && $item[0] === $method && $item[1] === $uri) {

                foreach ($item[4] as $middleware) {
                    try {
                        (globalContainer()->get($middleware))($request);
                    } catch (\Illuminate\Container\EntryNotFoundException $e) {
                        throw new \RuntimeException('Middleware class not found in container', 500, $e);
                    }
                }
            }
        }
    }
}
