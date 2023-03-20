#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Countries;

use Boilerwork\Persistence\Adapters\ElasticSearch\ElasticSearchAdapter;
use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;

use function var_dump;

final class CountriesService implements CountriesInterface
{
    public const INDEX_NAME = 'all_countries';

    public function __construct(
        private readonly ElasticSearchAdapter $client,
    ) {
    }

    public function getCountryById(string $id): CountryDto|CountryDtoNotFound
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body'  => [
                'query' => [
                    'term' => [
                        'iso_alpha_2' => trim($id),
                    ],
                ],
            ],
        ];

        $response = $this->client->search($params);

        $hits = $response['hits']['hits'];

        if (count($hits) > 0) {
            // Retrieve first result, assuming there will be only one
            $hit = $hits[0]['_source'];

            $country = new CountryDto(
                id: $hit['iso_alpha_2'],
                isoAlpha2: Iso31661Alpha2::fromString($hit['iso_alpha_2']),
                nameTranslations: MultiLingualText::fromArray([
                    'ES' => $hit['country_es'],
                    'EN' => $hit['country_en'],
                ]),
            );

            return $country;
        }

        return new CountryDtoNotFound();
    }
}