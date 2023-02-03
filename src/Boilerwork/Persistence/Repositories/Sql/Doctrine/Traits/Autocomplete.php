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
        $langSearch = $autocompleteDto->langSearchLowerCase();

        $this->addAutocompleteFilter($text, $langSearch);

        return $this;
    }

    private function addAutocompleteFilter(string $text, string $langSearch): QueryBuilder
    {
        $query = $this->queryBuilder
            ->where(
                sprintf(
                    "autocomplete_%s  LIKE '%s%%'",
                    $langSearch,
                    $text
                )
            )
            ->orderBy(
                'autocomplete_order',
                'DESC'
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