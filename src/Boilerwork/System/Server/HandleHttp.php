#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Server;

use Boilerwork\Domain\CustomAssertionFailedException;
use Boilerwork\Helpers\Environments;
use Boilerwork\System\Http\Request;
use Boilerwork\System\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

/**
 *
 **/
final class HandleHttp
{
    private \FastRoute\Dispatcher $httpDispatcher;

    public function __construct(string $routesPath)
    {
        // Init Routing
        $this->httpDispatcher = $this->initRouting($routesPath);
    }

    private function initRouting(string $routesPath): \FastRoute\Dispatcher
    {
        // Init Routing
        return \FastRoute\simpleDispatcher(
            function (\FastRoute\RouteCollector $r) use ($routesPath) {
                $routes = include($routesPath);
                foreach ($routes as $route) {
                    $r->addRoute($route[0], $route[1], $route[2]);
                }
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
                // var_dump($e->getErrorExceptions());
                $response->setStatusCode(400);
                $result = [
                    "error" =>
                    [
                        "code" => "validationError",
                        "message" => "Request is invalid or malformed",
                        "errors" => json_decode($e->getMessage())
                    ]
                ];
            } else {
                // // https://jsonapi.org/examples/#error-objects
                $response->setStatusCode($e->getCode());
                $result = [
                    "error" =>
                    [
                        "code" => "serverError",
                        "message" => $e->getMessage(),
                        "errors" => []
                    ]
                ];

                if ($_ENV['APP_DEBUG'] === 'true') {
                    array_push($result['error']['errors'], [
                        "message" =>  $e->getMessage(),
                        "file" => $e->getFile(),
                        "line" => $e->getLine(),
                        // "trace" => $e->getTrace(),
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
                if (is_array($handler)) {
                    // Custom method in class
                    $className = $handler[0];
                    $method = $handler[1];
                    $class = \Boilerwork\System\Container\Container::getInstance()->get($className);
                    $result = $class->$method($request, $vars);
                } else {
                    // invokable class  __invoke
                    $result = (\Boilerwork\System\Container\Container::getInstance()->get($handler))($request, $vars);
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
}
