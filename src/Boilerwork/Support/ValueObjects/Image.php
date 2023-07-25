#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\ValueObjects;

use Boilerwork\Foundation\ValueObjects\ValueObject;
use Boilerwork\Support\MultiLingualText;
use Boilerwork\Validation\Assert;

final class Image extends ValueObject
{
    private function __construct(
        private readonly string $url,
        private readonly MultiLingualText $description,
    ) {
        Assert::lazy()->tryAll()
            ->that($url)
            ->url('Url must be a valid URL format', 'image.invalidFormat')
            ->maxLength(2048, 'Value must have a maximum length of 2048 characters', 'image.invalidLength')
            ->verifyNow();
    }

    public static function fromScalars(string $url, string $description, string $language): self
    {
        return new self($url, MultiLingualText::fromSingleLanguageString($description, $language));
    }


    /**
     * @param  string  $url  URL.
     * @param  array<string, string>  $description  Multilingual description (ex. ['ES' => 'spanish text', 'EN' => 'english text']).
     */
    public static function fromArray(string $url, array $description): self
    {
        return new self($url, MultiLingualText::fromArray($description));
    }

    public function url(): string
    {
        return $this->url;
    }

    public function description(): MultiLingualText
    {
        return $this->description;
    }

    public function toArray($language = null): array
    {
        return [
            'url'        => $this->url,
            'description' => $this->description->toStringByLang($language),
        ];
    }
}
