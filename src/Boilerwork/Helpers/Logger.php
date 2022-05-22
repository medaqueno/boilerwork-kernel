<?php

declare(strict_types=1);

namespace Boilerwork\Helpers;

use DateTimeImmutable;
use Stringable;

class Logger
{
    public static function error(string|Stringable|array $message, $path = BASE_PATH . '/../../logs/', string $exception = \Exception::class, ?string $channel = 'error'): void
    {
        $d = new DateTimeImmutable();

        $message = is_array($message) ? json_encode($message) : ((method_exists($message, '__toString')) ? $message->__toString() : $message);
        $message = '[' . $d->format(DateTimeImmutable::ATOM) . '] ' . strtoupper($exception) . ' ' . $message . PHP_EOL;

        $fp = fopen($path . $channel . '_' . $d->format('Y-m-d') . '.log', 'a');
        stream_set_blocking($fp, false);

        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $message);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    public static function logger(string|Stringable|array $message, $path = BASE_PATH . '/../../logs/', string $mode = 'DEBUG', string $channel = 'default'): void
    {
        $d = new DateTimeImmutable();

        $message = is_array($message) ? json_encode($message) : ((method_exists($message, '__toString')) ? $message->__toString() : $message);
        $message = '[' . $d->format(DateTimeImmutable::ATOM) . '] ' . strtoupper($mode) . ' ' . $message . PHP_EOL;

        $fp = fopen($path . $channel . '_' . $d->format('Y-m-d') . '.log', 'a');
        stream_set_blocking($fp, false);

        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $message);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
