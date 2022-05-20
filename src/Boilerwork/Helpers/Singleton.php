#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Helpers;

trait Singleton
{
    private static self $instance;

    public static function getInstance(mixed ...$args): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static(...$args);
        }

        return self::$instance;
    }

    public function __clone()
    {
        throw new \Exception("Can't clone a singleton");
    }
}
