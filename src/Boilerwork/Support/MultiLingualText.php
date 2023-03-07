#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\Shared;

use function json_encode;

use const JSON_FORCE_OBJECT;
use const JSON_UNESCAPED_UNICODE;

final class MultiLingualText
{
    public static function fromString(string $text, string $language): string
    {
        return json_encode([$language => $text], JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }
}
