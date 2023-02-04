#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Repositories\Sql\Doctrine\Traits;


use Doctrine\DBAL\Query\QueryBuilder;
use Boilerwork\Persistence\QueryBuilder\AutocompleteDto;

trait Autocomplete
{
    public function addAutocomplete(AutocompleteDto $autocompleteDto): self
    {
        $text = $autocompleteDto->textLowerCase();
        $langSearch = $autocompleteDto->langSearch();
        $indexFields = $autocompleteDto->indexFields();

        $this->addAutocompleteFilter($text, $langSearch, $indexFields);

        return $this;
    }

    private function addAutocompleteFilter(string $text, string $langSearch, array $indexFields): QueryBuilder
    {

        $this->queryBuilder
            ->where(
                sprintf(
                    "%s  LIKE '%s%%'",
                    $indexFields[$langSearch],
                    $text
                )
            )
            ->setMaxResults(20);

        return $this->queryBuilder;
    }


    private function addAutocompleteFilterTsVector(string $text, string $vector, string $dictionary): QueryBuilder
    {
        $this->queryBuilder
            ->where(
                sprintf(
                    "%s @@ phraseto_tsquery('%s', '%s')",
                    $vector,
                    $dictionary,
                    $text
                )
            )
            ->orderBy(
                sprintf(
                    "ts_rank(%s, phraseto_tsquery('%s', '%s'))",
                    $vector,
                    $dictionary,
                    $text
                ),
                'DESC'
            )
            ->setMaxResults(10);

        return $this->queryBuilder;
    }

}