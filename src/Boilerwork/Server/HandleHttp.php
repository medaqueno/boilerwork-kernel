#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

use Boilerwork\Authentication\AuthInfo\AuthInfoNotFound;
use Boilerwork\Authentication\AuthInfo\Exceptions\AuthorizationException;
use Boilerwork\Container\IsolatedContainer;
use Boilerwork\Http\Request;
use Boilerwork\Http\Response;
use Boilerwork\Support\Exceptions\CustomException;
use Boilerwork\Validation\CustomAssertionFailedException;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
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

        try {
            $result = $this->handleRequest($request);

            foreach ($result->getHeaders() as $key => $value) {
                $response->setHeader($key, $value[0]);
            }

            $response->setStatusCode($result->getStatusCode(), $result->getReasonPhrase());
            $result = $result->getBody()->__toString();
        } catch (\Throwable $e) {

            error($e);

            if ($e instanceof CustomAssertionFailedException || $e instanceof \Assert\InvalidArgumentException) {
                $status = 422;
                $code =  "validationError";
                $message = "Request is invalid or malformed";
                $errors = json_decode($e->getMessage());
            } else if ($e instanceof CustomException) {
                $parse = json_decode($e->getMessage());

                $status = $e->getCode();
                $code =  $parse->error->code;
                $message =  $parse->error->message;
                $errors = [];
            } else {
                $status = $e->getCode();
                $code =  "serverError";
                $message = $e->getMessage();
                $errors = [];
            }

            $response->setStatusCode($status);
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
                    "message" =>  $e->getMessage(),
                    "file" => $e->getFile(),
                    "line" => $e->getLine(),
                    "trace" => env('TRACE_ERRORS') === "true" ? $e->getTrace() : null,
                ];
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

                $this->checkAuthorization(uri: $request_uri, method: $request_method);
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

                authInfo()->hasAuthorization($item[3]) === true ?: throw new AuthorizationException();

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
