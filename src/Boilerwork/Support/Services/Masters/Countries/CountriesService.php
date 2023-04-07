#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Countries;

use Boilerwork\Persistence\Adapters\ElasticSearch\ElasticSearchAdapter;
use Boilerwork\Support\ValueObjects\Geo\Country\Country;
use Boilerwork\Support\ValueObjects\Identity;

use function trim;

final class CountriesService implements CountriesInterface
{
    public const INDEX_NAME = 'all_countries';

    public function __construct(
        private readonly ElasticSearchAdapter $client,
    ) {
    }

    public function getCountryById(string $id): CountryEntity|CountryEntityNotFound
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

            return new CountryEntity(
                id: Identity::fromString($hit['id']),
                country: Country::fromScalarsWithIso31661Alpha2(
                    name: [
                        'ES' => $hit['country_es'],
                        'EN' => $hit['country_en'],
                    ],
                    iso31661Alpha2: $hit['iso_alpha_2'],
                    latitude: $hit['coordinates']['lat'],
                    longitude: $hit['coordinates']['lon'],
                )
            );
        }

        return new CountryEntityNotFound();
    }
}