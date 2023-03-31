#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support;

use Boilerwork\Support\ValueObjects\Language\Language;
use Boilerwork\Validation\Assert;

use function json_encode;
use function json_decode;
use function array_merge;

readonly class MultiLingualText
{
    private function __construct(private array $texts)
    {
    }

    /**
     * Crea un ValueObject vacío a partir de un texto y un idioma.
     *
     * @throws LazyAssertionException
     */
    public static function fromSingleLanguageString(string $text, string $language = Language::FALLBACK): self
    {
        Assert::lazy()
            ->tryAll()
            ->that($language, 'language.invalidIso3166Alpha2')
            ->notEmpty('Language must not be empty')
            ->that($text, 'text.invalidText')
            ->notEmpty('Text must not be empty')
            ->verifyNow();

        return new self([$language => $text]);
    }

    /**
     * Crea un ValueObject a partir de un array
     */
    public static function fromArray(array $texts): self
    {
        return new self($texts);
    }

    /**
     * Crea un ValueObject a partir de una cadena JSON.
     *
     * @throws LazyAssertionException
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);
        Assert::lazy()
            ->tryAll()
            ->that($data, 'texts.invalidJson')
            ->isArray('Invalid JSON format')
            ->verifyNow();

        return new self($data);
    }

    /**
     * Añade un texto en un idioma específico.
     *
     * @throws LazyAssertionException
     */
    public function addText(string $text, string $language = Language::FALLBACK): self
    {
        Assert::lazy()
            ->tryAll()
            ->that($language, 'language.invalidIso3166Alpha2')
            ->notEmpty('Language must not be empty')
            ->that($text, 'text.invalidText')
            ->notEmpty('Text must not be empty')
            ->verifyNow();

        $newTexts = $this->texts;
        $newTexts[$language] = $text;

        return new self($newTexts);
    }

    /**
     * Añade o reemplaza los valores a partir de un array.
     *
     * @throws LazyAssertionException
     */
    public function addOrReplaceFromArray(array $texts): self
    {
        $newTexts = array_merge($this->texts, $texts);

        return new self($newTexts);
    }

    /**
     * Devuelve el array con todos los valores.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->texts;
    }

    /**
     * Devuelve el texto en el idioma requerido.
     *
     * @throws LazyAssertionException
     */
    public function getText(string $language = Language::FALLBACK): ?string
    {
        Assert::lazy()
            ->tryAll()
            ->that($language, 'language.invalidIso3166Alpha2')
            ->notEmpty('Language must not be empty')
            ->verifyNow();

        return $this->texts[$language] ?? null;
    }

    /**
     * Devuelve el objeto en formato JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->texts, JSON_THROW_ON_ERROR);
    }

    /**
     * Devuelve el texto y el idioma requerido en formato JSON.
     * @example: { 'ES': 'Text Localised' }
     *
     * @throws LazyAssertionException
     */
    public function getJsonTextByLanguage(string $language = Language::FALLBACK): ?string
    {
        $text = $this->getText($language);

        if ($text === null) {
            return null;
        }

        return json_encode([$language => $text], JSON_THROW_ON_ERROR);
    }
}
