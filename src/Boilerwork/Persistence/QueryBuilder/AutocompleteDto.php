#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\QueryBuilder;

use Boilerwork\Validation\Assert;

final class AutocompleteDto
{
    private function __construct(
        private readonly string $text,
        private readonly string $langSearch
    )
    {
        Assert::lazy()
            ->that($text)
            ->notEmpty('Text is mandatory', 'autocompleteText.invalidValue')
            ->that($langSearch)
            ->inArray(static::$searchLanguages, 'Value must be a supported search language: ES ', 'autocompleteLangSearch.invalidValue')
            ->verifyNow();

    }

    protected static $searchLanguages = array('ES');


    public function text(): string
    {
        return $this->text;
    }

    public function langSearch(): string
    {
        return $this->langSearch;
    }

    public function langSearchLowerCase(): string
    {
        return strtolower($this->langSearch);
    }

    public function textLowerCase(): string
    {
        return strtolower($this->text);
    }

    public static function fromData(string $text, ?string $lang): static
    {
        $iso = $lang ?? 'ES';

        return new static (
            text: $text,
            langSearch: strtoupper($iso)
        );
    }
}