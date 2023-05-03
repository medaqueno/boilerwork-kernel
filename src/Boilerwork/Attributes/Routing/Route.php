#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Attributes\Routing;

use Attribute;
use Boilerwork\Authorization\AuthorizationsMiddleware;
use Boilerwork\Validation\Assert;
use Boilerwork\Router\Adapter\FastRoute;

#[Attribute(Attribute::TARGET_ALL)]
final readonly class Route
{
    /**
     * @example
     *  Route(
    method: 'POST',
    route: 'auth/login',
    authorizations: [AuthorizationsProvider::PUBLIC],
    )
     */
    public function __construct(
        private string $method,
        private string $route,
        private array $authorizations = [],
        private ?string $target = null,
    ) {
        Assert::that($method)->choice(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTION', 'TRACE', 'HEAD'], 'Method parameter value in Attribute Route is not valid');
        Assert::that($route)->notEmpty('Route parameter value in Attribute Route must not be empty');

        FastRoute::addRoute($method, $route, $target, $authorizations);
        AuthorizationsMiddleware::addRoute($method, $route, $authorizations);
    }
}
