#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support;

use Boilerwork\Support\ValueObjects\Language\Language;
use Boilerwork\Validation\Assert;

use function array_diff;
use function array_filter;
use function array_keys;
use function array_merge;
use function array_values;
use function current;
use function implode;
use function json_decode;
use function json_encode;

readonly class MultiLingualText
{
    private function __construct(private array $texts)
    {
        Assert::lazy()
            ->tryAll()
            ->that($texts)
            ->satisfy(
                function () use ($texts) {
                    $diff = array_diff(array_keys($texts), Language::ACCEPTED_LANGUAGES);

                    return empty($diff);
                },
                'Language must be: ' . implode(',', Language::ACCEPTED_LANGUAGES),
                'language.invalidIso3166Alpha2'
            )
            ->satisfy(
                function () use ($texts) {
                    $filteredInput = array_filter($texts, function ($value) {
                        return !empty($value);
                    });

                    return count($texts) === count($filteredInput);
                },
                'Text must not be empty',
                'text.notEmpty'
            )
            ->verifyNow();
    }

    /**
     * Creates an empty ValueObject from a text and a language.
     * If the text does not exist, the character '-' is returned.
     *
     * @throws LazyAssertionException
     */
    public static function fromSingleLanguageString(?string $text, string $language = Language::FALLBACK): self
    {
        $text = $text ?? '-';
        Assert::lazy()
            ->tryAll()
            ->that($text)
            ->notEmpty('Text must not be empty', 'text.notEmpty')
            ->verifyNow();

        return new self([$language => $text]);
    }

    /**
     * Creates a ValueObject from an array.
     */
    public static function fromArray(array $texts): self
    {
        return new self($texts);
    }

    /**
     * Creates a ValueObject from a JSON string.
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
     * Adds a text in a specific language.
     *
     * @throws LazyAssertionException
     */
    public function addText(string $text, string $language = Language::FALLBACK): self
    {
        Assert::lazy()
            ->tryAll()
            ->that($text, 'text.notEmpty')
            ->notEmpty('Text must not be empty')
            ->verifyNow();

        $newTexts            = $this->texts;
        $newTexts[$language] = $text;

        return new self($newTexts);
    }

    /**
     * Adds or replaces values from an array.
     *
     * @throws LazyAssertionException
     */
    public function addOrReplaceFromArray(array $texts): self
    {
        $newTexts = array_merge($this->texts, $texts);

        return new self($newTexts);
    }

    /**
     * Returns a new instance of MultiLingualText ensuring that the default language is present in the texts array.
     *
     * @param  string  $defaultLanguage  The default language code (e.g., 'ES')
     *
     * @return self
     */
    public function withDefaultLanguage(string $defaultLanguage = Language::FALLBACK): self
    {
        if (isset($this->texts[$defaultLanguage])) {
            return $this;
        }

        $firstText = $this->getDefaultText();
        if ($firstText === null) {
            return $this;
        }

        $newTexts                   = $this->texts;
        $newTexts[$defaultLanguage] = $firstText;

        return new self($newTexts);
    }

    /**
     * Returns a new instance of MultiLingualText ensuring that all accepted languages are present in the texts array.
     * If a language is not present in the texts array, the default text will be added for that language.
     *
     * @param  array  $acceptedLanguages  The array of accepted language codes (e.g., ['ES', 'EN', 'FR'])
     *
     * @return self
     */
    public function withAcceptedLanguages(array $acceptedLanguages = Language::ACCEPTED_LANGUAGES): self
    {
        $newTexts    = $this->texts;
        $defaultText = $this->getDefaultText();

        foreach ($acceptedLanguages as $language) {
            if (!isset($newTexts[$language])) {
                $newTexts[$language] = $defaultText;
            }
        }

        return new self($newTexts);
    }

    /**
     * Returns the default text, which is the first available text in the array.
     *
     * @return string|null The default text or null if no texts are available
     */
    private function getDefaultText(): ?string
    {
        return current(array_values($this->texts)) ?: null;
    }


    /**
     * Returns the text in the required language
     *
     * @throws LazyAssertionException
     */
    public function toStringByLang(string $language = Language::FALLBACK): ?string
    {
        Assert::lazy()
            ->tryAll()
            ->that($language)
            ->inArray(
                Language::ACCEPTED_LANGUAGES,
                'Language must be: ' . implode(',', Language::ACCEPTED_LANGUAGES),
                'language.invalidIso3166Alpha2',
            )
            ->verifyNow();

        return $this->texts[$language] ?? null;
    }

    /**
     * Returns the array with all values.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->texts;
    }

    /**
     * Returns the object in JSON format.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->texts);
    }

    /**
     * Returns the text and the required language in JSON format.
     * @throws LazyAssertionException
     * @example: { 'ES': 'Text Localised' }
     *
     */
    public function toJsonByLang(string $language = Language::FALLBACK): ?string
    {
        $text = $this->toStringByLang($language);

        if ($text === null) {
            return null;
        }

        return json_encode([$language => $text], JSON_THROW_ON_ERROR);
    }
}
