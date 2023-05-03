#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public static function addRoute(string $method, string $route, string $handler, array $authorizations): void;

    public function getRoutes(): array;

    public function retrieveHandler(ServerRequestInterface $request): mixed;
}
