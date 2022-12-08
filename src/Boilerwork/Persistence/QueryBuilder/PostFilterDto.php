#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Persistence\QueryBuilder;

/**
 * Convert query string tiwht the format:
 * &filter=category=3-4;price=min-300;facilities=1;facilities=4;facilities=6;category=1-6
 *
 * To an array:
 *  [
 *      'category' => ['3-4','1-6'],
 *      'price' => ['min-300'],
 *      'facilities' => ['1','4','6']
 *  ]
 */
final class PostFilterDto
{
    /** @var array<string, array<mixed>> $filters  */
    private function __construct(
        private readonly array $filters,
    ) {
    }

    public function filters(): array
    {
        return $this->filters;
    }

    /**
     * @param string $params Query string
     * @param array $acceptedParams Name or parameters that are accepted and added to DTO.
     * @return static
     */

    public static function create(string $params, array $acceptedParams = []): static
    {
        $parsed = [];

        $temp = explode(';', $params);
        foreach ($temp as $item) {
            /** @var array<string, mixed> */
            $exploded = explode('=', $item);

            // Capture operator between [foo] -> $output_array[0]
            // preg_match('/(?<=\[)(.*?)(?=\])/', $exploded[0], $output_array)

            if (!in_array($exploded[0], $acceptedParams)) {
                continue;
            }

            array_key_exists($exploded[0], $parsed)  ?
                array_push($parsed[$exploded[0]], $exploded[1]) :
                $parsed[$exploded[0]][] = $exploded[1];
        }

        return new static(
            filters: $parsed,
        );
    }
}
