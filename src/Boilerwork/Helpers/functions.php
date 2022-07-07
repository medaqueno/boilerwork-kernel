#!/usr/bin/env php
<?php

declare(strict_types=1);

use Boilerwork\System\AuthInfo\AuthInfo;

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
    function container()
    {
        return \Boilerwork\System\Container\Container::getInstance();
    }
}

if (!function_exists('getAuthInfo')) {
    function getAuthInfo(): AuthInfo
    {
        return container()->get('AuthInfo');
    }
}
