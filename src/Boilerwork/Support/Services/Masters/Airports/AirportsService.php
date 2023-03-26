#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Airports;

use Boilerwork\Persistence\Repositories\MastersRepository;
use Boilerwork\Support\MultiLingualText;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Support\ValueObjects\Identity;

use function json_decode;
use function var_dump;

final class AirportsService implements AirportsInterface
{
    public function __construct(
        private readonly MastersRepository $repository,
    ) {
    }

    public function getAirportByIATA(string $iata): AirportDto|AirportDtoNotFound
    {
        $response = $this->repository->queryBuilder
            ->select(
                'id',
                "data -> 'info' ->> 'iata' as iata'",
                "data -> 'name' as airport_name",
                "data -> 'location' ->> 'id' as location_id",
                "data -> 'location' -> 'name' as location_name",
                "data -> 'country' ->> 'iso_alpha2' as iso_alpha_2",
                "data -> 'country' -> 'name' as country_name",
            )
            ->from('all_airports')
            ->where("data -> 'info' ->> 'iata' = :where_iata")
            ->andWhere("data -> 'country' ->> 'iso_alpha2' IS NOT NULL")
            ->andWhere("data -> 'location' ->> 'id' IS NOT NULL")
            ->setParameters([
                "where_iata" => $iata,
            ])
            ->limit(1)
            ->fetchAssociative();

        if ($response !== false) {
            return new AirportDto(
                id: Identity::fromString($response['id']),
                iata: $response['iata'],
                nameTranslations: MultiLingualText::fromArray(json_decode($response['airport_name'], true)),
                locationNameTranslations: MultiLingualText::fromArray(json_decode($response['location_name'], true)),
                locationId: Identity::fromString($response['location_id']),
                isoAlpha2: Iso31661Alpha2::fromString($response['iso_alpha_2']),
                countryNameTranslations: MultiLingualText::fromArray(json_decode($response['country_name'], true)),
            );
        }

        return new AirportDtoNotFound();
    }
}
