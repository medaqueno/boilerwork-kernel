#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Server;

use Attribute;
use Boilerwork\Validation\Assert;

/**
 * WIP
 * @package Boilerwork\Server
 */
#[Attribute(Attribute::TARGET_CLASS, Attribute::IS_REPEATABLE)]
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
    ) {
        Assert::that($method)->choice(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTION'], 'Method parameter value in Attribute Route is not valid');
        Assert::that($route)->notEmpty('Route parameter value in Attribute Route must not be empty');
    }

    public function __invoke(string $target)
    {
        echo "\n ROUTES: ";
        // var_dump([$this->method, $this->route, $target, $this->authorizations, []]);

        RouterMiddleware::getInstance(
            [[$this->method, $this->route, $target, $this->authorizations, []]]
        );
    }
}
