#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Locations;

use Boilerwork\Persistence\Adapters\ElasticSearch\ElasticSearchAdapter;
use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Support\ValueObjects\Identity;

final class LocationsService implements LocationsInterface
{
    public const LOCATIONS_INDEX = 'all_locations';

    public function __construct(
        private readonly ElasticSearchAdapter $client,
    ) {
    }

    public function getLocationById(string $id): LocationDto|LocationDtoNotFound
    {
        $params = [
            'index' => self::LOCATIONS_INDEX,
            'body'  => [
                'query' => [
                    'term' => [
                        'id' => trim($id),
                    ],
                ],
            ],
        ];

        $response = $this->client->search($params);
        $hits     = $response['hits']['hits'];

        if (count($hits) > 0) {
            // Retrieve first result, assuming there will be only one
            $hit = $hits[0]['_source'];

            $location = new LocationDto(
                id: Identity::fromString($hit['id']),
                isoAlpha2: Iso31661Alpha2::fromString($hit['iso_alpha_2']),
                nameTranslations: MultiLingualText::fromArray([
                    'ES' => $hit['location_es'],
                    'EN' => $hit['location_en'],
                ]),
                coordinates: Coordinates::fromScalars(
                    latitude: $hit['coordinates']['lat'],
                    longitude: $hit['coordinates']['lon'],
                )
            );

            return $location;
        }

        return new LocationDtoNotFound();
    }
}
