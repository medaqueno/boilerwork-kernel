#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Services\Masters\Locations;

use Boilerwork\Persistence\Adapters\ElasticSearch\ElasticSearchAdapter;
use Boilerwork\Support\ValueObjects\Geo\Coordinates;
use Boilerwork\Support\ValueObjects\Geo\Country\Iso31661Alpha2;
use Boilerwork\Support\ValueObjects\Geo\Location;
use Boilerwork\Support\ValueObjects\Identity;

use function count;
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

        return $this->buildLocationEntity($response['hits']['hits']);
    }

    /**
     * Esta consulta busca ubicaciones que coincidan con el nombre de la ubicación y aplica filtros y ponderaciones en función de la distancia, el código del país (si se proporciona) y la población.
     *
     * Coincidencias:
     *
     * Busca resultados que coincidan con la frase exacta o parcial del nombre de la ubicación en los campos location_es y location_en.
     * La consulta busca coincidencias de frases exactas con un boost de 10.
     * También busca coincidencias de términos con un boost de 5.
     * La cláusula should dentro de must hace que la consulta busque coincidencias de frases o términos en cualquier campo, pero las coincidencias de frases tendrán una mayor ponderación en la puntuación.
     *
     * Filtros:
     *
     * Filtra los resultados según las siguientes condiciones:
     * Los resultados deben estar dentro de un radio de 30 km desde las coordenadas dadas.
     * Si se proporciona un valor iso_alpha_2, los resultados deben tener ese código de país.
     * (Comentado) Los resultados deben tener población mayor de cero.
     *
     * Ponderaciones:
     *
     * Utiliza la función function_score para modificar la puntuación de los resultados de la consulta en función de:
     * La cercanía a las coordenadas dadas usando una función gaussiana con un scale de 10 km y un weight de 3. Esto significa que los resultados más cercanos tendrán una mayor puntuación. La escala (scale) es la distancia a la cual la puntuación decaerá a la mitad del valor máximo.
     * La población mayor a cero con un weight de 2. Los resultados con una población mayor que cero tendrán una puntuación más alta.
     * (Comentado) La mayor población utilizando field_value_factor, que ajusta la puntuación según el valor del campo de población con un modifier de raíz cuadrada y un factor de 1.5. Los resultados con una mayor población tendrán una puntuación más alta.
     *
     * Multiplica las puntuaciones calculadas por las funciones con la puntuación de la consulta original utilizando el modo boost_mode como "multiply".
     *
     * Orden:
     *
     * Ordena los resultados por la puntuación total en orden descendente.
     *
     * Devuelve solo 1 resultado LocationEntity, como se especifica en el tamaño size o bien un LocationEntityNotFound
     *
     */
    public function searchSimilarLocation(
        string $locationName,
        Coordinates $coordinates,
        ?Iso31661Alpha2 $iso3166Alpha2,
    ): LocationEntity {

        $params = [
            'index' => [self::LOCATIONS_INDEX],
            'body'  => [
                'query' => [
                    'function_score' => [
                        'query'     => [
                            'bool' => [
                                'must'   => [
                                    [
                                        'bool' => [
                                            'should' => [
                                                [
                                                    'bool' => [
                                                        'should' => [
                                                            [
                                                                'match_phrase' => [
                                                                    'location_es' => [
                                                                        'query' => $locationName,
                                                                        'boost' => 10,
                                                                    ],
                                                                ],
                                                            ],
                                                            [
                                                                'match_phrase' => [
                                                                    'location_en' => [
                                                                        'query' => $locationName,
                                                                        'boost' => 10,
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'bool' => [
                                                        'should' => [
                                                            [
                                                                'match' => [
                                                                    'location_es' => [
                                                                        'query' => $locationName,
                                                                        'boost' => 5,
                                                                    ],
                                                                ],
                                                            ],
                                                            [
                                                                'match' => [
                                                                    'location_en' => [
                                                                        'query' => $locationName,
                                                                        'boost' => 5,
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                'filter' => [
                                    [ // Eliminamos cualquier resultado a una distancia mayor de 30km
                                        'geo_distance' => [
                                            'distance'    => '30km',
                                            'coordinates' => [
                                                'lat' => $coordinates->latitude(),
                                                'lon' => $coordinates->longitude(),
                                            ],
                                        ],
                                    ],
                                    //                                    [
                                    //                                        'range' => [
                                    //                                            'population' => ['gt' => 0],
                                    //                                        ],
                                    //                                    ],
                                ],
                            ],
                        ],
                        'functions' => [
                            [ // Sumamos score a los resultados por cercanía
                                'gauss'  => [
                                    'coordinates' => [
                                        'origin' => [
                                            'lat' => $coordinates->latitude(),
                                            'lon' => $coordinates->longitude(),
                                        ],
                                        'scale'  => '10km',
                                    ],
                                ],
                                'weight' => 3,
                            ],
                            [ // Sumamos score a los resultados con población mayor de cero
                                'filter' => [
                                    'range' => [
                                        'population' => [
                                            'gt' => 0,
                                        ],
                                    ],
                                ],
                                'weight' => 2,
                            ],
                            //                            [ // Sumamos score a los resultados con mayor población.
                            //                                'field_value_factor' => [
                            //                                    'field' => 'population',
                            //                                    'modifier' => 'sqrt',
                            //                                    'factor' => 1.5,
                            //                                ],
                            //                                'weight' => 1,
                            //                            ],
                        ],

                        'boost_mode' => 'multiply',
                    ],
                ],
                'sort'  => [
                    ['_score' => ['order' => 'desc']],
                    //                    ['population' => ['order' => 'desc']],
                ],
            ],
            'size'  => 2,
        ];


        if ($iso3166Alpha2) {
            $params['body']['query']['function_score']['query']['bool']['filter'][] =
                [
                    'term' => [
                        'iso_alpha_2' => $iso3166Alpha2->toString(),
                    ],
                ];
        }


        $response = $this->client->search($params);

        return $this->buildLocationEntity($response['hits']['hits']);
    }

    private function buildLocationEntity($hits): LocationEntity
    {
        if (count($hits) > 0) {

            // Retrieve first result, assuming it is the best result
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
