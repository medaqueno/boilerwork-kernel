#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Locations;

use Boilerwork\Persistence\Adapters\ElasticSearch\ElasticSearchAdapter;
use Boilerwork\Support\ValueObjects\Geo\Location;
use Boilerwork\Support\ValueObjects\Identity;

use function trim;

final class LocationsService implements LocationsInterface
{
    public const LOCATIONS_INDEX = 'all_locations';

    public function __construct(
        private readonly ElasticSearchAdapter $client,
    ) {
    }

    public function getLocationById(string $id): LocationEntity|LocationEntityNotFound
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

            return new LocationEntity(
                id: Identity::fromString($hit['id']),
                location: Location::fromScalars(
                    name: [
                        'ES' => $hit['location_es'],
                        'EN' => $hit['location_en'],
                    ],
                    iso31661Alpha2: $hit['iso_alpha_2'],
                    latitude: $hit['coordinates']['lat'],
                    longitude: $hit['coordinates']['lon'],
                )
            );
        }

        return new LocationEntityNotFound();
    }
}