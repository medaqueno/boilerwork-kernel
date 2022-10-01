#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Authentication\AuthInfo\AuthInfo;
use Boilerwork\Container\IsolatedContainer;
use Boilerwork\Support\Logs\Logger;
use Boilerwork\Messaging\MessagePublisher;

if (!function_exists('env')) {
    function env(string $name, mixed $defaultValue = null): mixed
    {
        if (isset($_ENV[$name])) {
            return $_ENV[$name];
        } else if (getenv($name) !== false) {
            return getenv($name);
        }

        return $defaultValue;
    }
}

if (!function_exists('getMemoryStatus')) {
    // Monitor memory
    function getMemoryStatus(): void
    {
        $fh = fopen('/proc/meminfo', 'r');

        if ($fh) {
            $memTotal = 0;
            while ($line = fgets($fh)) {
                $pieces = array();

                if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                    $memTotal = $pieces[1];
                    break;
                }
            }
            fclose($fh);

            printf("\n Memory Total: %s kB\n Memory Usage: %s kB\n Memory Peak: %s kB\n\n", $memTotal, memory_get_usage() / 1024, memory_get_peak_usage(true) / 1024);
        }
    }
}

if (!function_exists('container')) {
    /**
     * Return Container instance to be used in local/isolated requests/jobs/messages
     * @return IsolatedContainer
     */
    function container()
    {
        return (\Boilerwork\Container\Container::getInstance())->getIsolatedContainer();
    }
}

if (!function_exists('globalContainer')) {
    /**
     * Return Global Container instance with shared data across application memory
     *
     **/
    function globalContainer()
    {
        return \Boilerwork\Container\Container::getInstance();
    }
}

if (!function_exists('authInfo')) {
    function authInfo(): AuthInfo
    {
        return container()->get('AuthInfo');
    }
}

if (!function_exists('eventsPublisher')) {
    function eventsPublisher(): MessagePublisher
    {
        return MessagePublisher::getInstance();
    }
}

if (!function_exists('base_path')) {
    /**
     * @return string Path from /src
     **/
    function base_path(string $path = ''): string
    {
        // Defined in Application Start
        return BASE_PATH . $path;
    }
}

if (!function_exists('error')) {
    function error(string|Stringable|array $message, string $exception = \Throwable::class, ?string $channel = 'error'): void
    {
        $debug = env('APP_DEBUG') ?? false;
        if (boolval($debug) === false) {
            return;
        }

        Logger::error(message: $message, path: base_path('/logs/'), exception: $exception, channel: $channel);
    }
}

if (!function_exists('logger')) {
    function logger(string|Stringable|array $message, string $mode = 'DEBUG', string $channel = 'default'): void
    {
        $debug = env('APP_DEBUG') ?? false;
        if (boolval($debug) === false) {
            return;
        }

        Logger::logger(message: $message, path: base_path('/logs/'), mode: $mode, channel: $channel);
    }
}
