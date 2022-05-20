#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\Events\EventPublisher;
use Boilerwork\System\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

if (!function_exists('app')) {
    function app(): \Bootstrap\Application
    {
        return \Bootstrap\Application::getInstance();
    }
}
if (!function_exists('base_path')) {
    /**
     * @return string Path from /src
     **/
    function base_path(string $path): string
    {
        return __DIR__ . '/../..' . $path;
    }
}
if (!function_exists('error')) {
    function error(string|Stringable|array $message, string $exception = \Exception::class, ?string $channel = 'error'): void
    {
        $debug = $_ENV['APP_DEBUG'] ?? false;
        if (boolval($debug) === true) {
            $d = new DateTimeImmutable();

            $message = is_array($message) ? json_encode($message) : ((method_exists($message, '__toString')) ? $message->__toString() : $message);
            $message = '[' . $d->format(DateTime::ATOM) . '] ' . strtoupper($exception) . ' ' . $message . PHP_EOL;

            $fp = fopen(base_path('/logs/') . $channel . '_' . $d->format('Y-m-d') . '.log', 'a');
            stream_set_blocking($fp, false);

            if (flock($fp, LOCK_EX)) {
                fwrite($fp, $message);
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
}

if (!function_exists('logger')) {
    function logger(string|Stringable|array $message, string $mode = 'DEBUG', string $channel = 'default'): void
    {
        $debug = $_ENV['APP_DEBUG'] ?? false;
        if (boolval($debug) === true) {
            $d = new DateTimeImmutable();

            $message = is_array($message) ? json_encode($message) : ((method_exists($message, '__toString')) ? $message->__toString() : $message);
            $message = '[' . $d->format(DateTime::ATOM) . '] ' . strtoupper($mode) . ' ' . $message . PHP_EOL;

            $fp = fopen(base_path('/logs/') . $channel . '_' . $d->format('Y-m-d') . '.log', 'a');
            stream_set_blocking($fp, false);

            if (flock($fp, LOCK_EX)) {
                fwrite($fp, $message);
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
}
if (!function_exists('responseEmpty')) {

    function responseEmpty(int $status = 204, array $headers = []): ResponseInterface
    {
        return Response::empty(status: $status, headers: $headers)->withHeader('Content-type', 'text/plain');
    }
}

if (!function_exists('responseJson')) {
    function responseJson(mixed $data = '', int $status = 200, array $headers = []): ResponseInterface
    {
        return Response::json(data: $data, status: $status, headers: $headers);
    }
}
if (!function_exists('responseText')) {
    function responseText(string|StreamInterface $text = '', int $status = 200, array $headers = []): ResponseInterface
    {
        return Response::text(text: $text, status: $status, headers: $headers);
    }
}
if (!function_exists('eventsPublisher')) {
    function eventsPublisher()
    {
        return EventPublisher::getInstance();
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
