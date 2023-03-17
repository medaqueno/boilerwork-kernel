#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support;

use function json_encode;

use const JSON_FORCE_OBJECT;
use const JSON_UNESCAPED_UNICODE;

final class MultiLingualText
{
    private array $translations;

    private function __construct(array $translations)
    {
        $this->translations = $translations;
    }

    public static function fromArray(array $translations): self
    {
        return new self($translations);
    }

    /**
     * @deprecated Use getTranslationInJson()
     */
    public static function fromString(string $text, string $language): string
    {
        return self::getTranslationInJson($text, $language);
    }

    /**
     * Returns a json string like:
     * { 'ES': 'Text Localised' }
     */
    public static function getTranslationInJson(string $text, string $language): string
    {
        return json_encode([$language => $text], JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }

    public function getTranslation(string $language): ?string
    {
        return $this->translations[$language] ?? null;
    }

    public function translationsToArray(): array
    {
        return $this->translations;
    }
}
