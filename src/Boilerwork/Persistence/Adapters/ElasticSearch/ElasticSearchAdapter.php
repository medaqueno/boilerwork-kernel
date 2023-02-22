#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\Adapters\ElasticSearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Http\Promise\Promise;

final class ElasticSearchAdapter
{
    public Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([
                sprintf('%s:%d', env('ELASTIC_SEARCH_HOST'), env('ELASTIC_SEARCH_PORT')),
            ])
            ->build();
    }

    public function createIndex(string $indexName, array $settings = [], array $mappings = [])
    {
        $params = [];
        if (count($settings) >  0) {
            $params['settings'] = $settings;
        }
        if (count($mappings) >  0) {
            $params['mappings'] = $mappings;
        }

        return $this->client->indices()->create(
            [
                'index' => $indexName,
                $params
            ]
        );
    }

    public function deleteIndexes(string ...$indexNames): Elasticsearch|Promise
    {
        return $this->client->indices()->delete(['index' => $indexNames]);
    }

    public function updateSettings(string $indexName, array $settings = []): Elasticsearch|Promise
    {
        $this->client->indices()->close([
            'index' => $indexName
        ]);

        $settings = [
            'index' => $indexName,
            'body' => $settings,
        ];

        $response = $this->client->indices()->putSettings($settings);

        $this->client->indices()->open([
            'index' => $indexName
        ]);
        $this->client->indices()->refresh([
            'index' => $indexName
        ]);

        return $response;
    }

    public function getSettings(string ...$indexNames): Elasticsearch|Promise
    {
        return $this->client->indices()->getSettings(['index' => $indexNames]);
    }

    public function updateMappings(string $indexName, array $mapping = []): Elasticsearch|Promise
    {
        $this->client->indices()->close([
            'index' => $indexName
        ]);

        $mapping = [
            'index' => $indexName,
            'body' => $mapping,
        ];

        $response = $this->client->indices()->putMapping($mapping);

        $this->client->indices()->open([
            'index' => $indexName
        ]);
        $this->client->indices()->refresh([
            'index' => $indexName
        ]);

        return $response;
    }

    public function getMappings(string ...$indexNames): Elasticsearch|Promise
    {
        return $this->client->indices()->getMapping(['index' => $indexNames]);
    }

    /**
     * @param string $indexName
     * @param array $documents
     *
     * Example documents
     * $cities = [
            ['id' => 1, 'city_name' => 'Madrid', 'pais_code' => 'ES'],
            ['id' => 2, 'city_name' => 'París', 'pais_code' => 'FR'],
        ];
     *
     * @return Elasticsearch|Promise
     * @throws NoNodeAvailableException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function bulk(string $indexName, array $documents): Elasticsearch|Promise
    {
        $params = ['body' => []];

        foreach ($documents as $document) {
            $params['body'][] = [
                'index' => [
                    '_index' => $indexName,
                    '_id' => $document['id'],
                ]
            ];

            $params['body'][] = $document;
        }

        return $this->client->bulk($params);
    }

    /**
     * Almacena un documento en Elasticsearch.
     *
     * @param string $indexName
     * @param array $documents
     *
     * Example document
     * $city = [
            ['id' => 1, 'city_name' => 'Madrid', 'pais_code' => 'ES'],
        ];
     *
     * @param string $indexName
     * @param array $document
     * @return Elasticsearch|Promise
     * @throws MissingParameterException
     * @throws NoNodeAvailableException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function index(string $indexName, array $document): Elasticsearch|Promise
    {
        return $this->client->index(
            [
                'index' => $indexName,
                'body'  => $document
            ]
        );
    }

    /**
     * Ejecuta una consulta de búsqueda en Elasticsearch.
     *
     * @param array $params Parámetros de la consulta de búsqueda.
     *
     * @return Elasticsearch|Promise
     */
    public function search(array $params): Elasticsearch|Promise
    {
        return $this->client->search($params);
    }

    /**
     * Devuelve el número de resultados de una consulta de búsqueda en Elasticsearch.
     *
     * @param array $params Parámetros de la consulta de búsqueda.
     *
     * @return int
     */
    public function count(array $params): int
    {
        return $this->client->count($params)['count'];
    }

    /**
     * Actualiza un documento existente en Elasticsearch.
     */
    public function update(string $indexName, array $document): Elasticsearch|Promise
    {
        return $this->client->update(
            [
                'index' => $indexName,
                'id' => $document['id'],
                'body' => ['doc' => $document]
            ]
        );
    }

    /**
     * Elimina un documento de Elasticsearch.
     *
     * @param string $index Nombre del índice.
     * @param string $id    ID del documento.
     *
     * @return Elasticsearch|Promise
     */
    public function delete(string $indexName, string $documentId): Elasticsearch|Promise
    {
        return $this->client->delete([
            'index' => $indexName,
            'id' => $documentId
        ]);
    }

    /**
     * Get Raw Elastic Client Instance
     * @return Client
     */
    public function raw(): Client
    {
        return $this->client;
    }
}
