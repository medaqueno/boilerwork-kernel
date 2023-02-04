#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\QueryBuilder;

use Boilerwork\Validation\Assert;

final class AutocompleteDto
{
    private $searchLanguages = array();
    private function __construct(
        private readonly string $text,
        private readonly string $langSearch,
        private readonly array $indexFields
    )
    {
        $indexBy = array_filter($indexFields);
        Assert::lazy()
            ->that($indexBy)
            ->notEmpty('indexFields is mandatory', 'indexFields.invalidValue')
            ->verifyNow();

        if ($indexBy) {
            foreach ($indexFields as $key => $value) {
                array_push($this->searchLanguages, $key);
                Assert::lazy()
                    ->that($key)
                    ->notEmpty('Lang key in  indexFields is mandatory', 'langKeyIndexFields.invalidValue')
                    ->that($value)
                    ->notEmpty('Lang vlue in  indexFields is mandatory', 'langValueIndexFields.invalidValue')
                    ->verifyNow();
            }
        }

        Assert::lazy()
            ->that($text)
            ->notEmpty('Text is mandatory', 'autocompleteText.invalidValue')
            ->that($langSearch)
            ->inArray($this->searchLanguages, 'Value must be a supported search language ', 'autocompleteLangSearch.invalidValue')
            ->verifyNow();
    }



    public function text(): string
    {
        return $this->text;
    }
    public function textLowerCase(): string
    {
        return strtolower($this->text);
    }

    public function langSearch(): string
    {
        return $this->langSearch;
    }

    public function indexFields(): array
    {
        return $this->indexFields;
    }

    public static function create(string $text, ?string $lang, array $indexFields = []): static
    {
        $iso = $lang ?? array_key_first($indexFields) ?? 'ES';

        return new static (
            text: $text,
            langSearch: strtoupper($iso),
            indexFields: $indexFields
        );
    }
}