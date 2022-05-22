#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Container;


use Psr\Container\ContainerInterface;
use Illuminate\Container\Container as ContainerImplementation;

/**
 * Dependency Injection Container
 *
 * https://laravel.com/api/9.x/Illuminate/Container/Container.html
 */
final class Container extends ContainerImplementation implements ContainerInterface
{
}
