#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Collection\Behaviours;

use Boilerwork\System\Collection\Collection;
use Boilerwork\System\Collection\Value;

trait Exports
{

    /**
     * Convert the collection and any nested data to an array
     *
     * Note: some objects may fail to convert to arrays if they do not have
     * appropriate export / array methods.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->items as $key => $value) {
            $array[$key] = Value::exportToArray($value);
        }

        return $array;
    }


    /**
     * Return the collection as a JSON string, uses toArray to convert to an Array
     *
     * @param int $options Any JSON_* constants
     *
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Returns a HTTP query string of the values
     *
     * Note: should only be used with elements that can be cast to scalars.
     *
     * @link https://www.php.net/http_build_query
     *
     * @param string $separator
     * @param int    $encoding
     *
     * @return string
     */
    public function toQueryString(string $separator = '&', int $encoding = PHP_QUERY_RFC3986): string
    {
        return http_build_query($this->toArray(), '', $separator, $encoding);
    }

    /**
     * Implodes all the values into a single string, objects should support __toString
     *
     * If a specific value is specified it will be pulled from any sub-arrays or
     * objects; alternatively it can be a closure to fetch specific properties from
     * any objects in the collection.
     *
     * If $withKeys is set to a string, it will prefix the string value with the key
     * and the $withKeys string.
     *
     * @param string               $glue
     * @param null|string|callable $value
     * @param null|string          $withKeys
     *
     * @return string
     */
    public function implode(string $glue = ',', string|callable $value = null, string $withKeys = null): string
    {
        $elements = [];

        $accessor = Value::accessor($value);

        foreach ($this->items as $key => $value) {
            $value = $accessor($value);

            if (null !== $withKeys) {
                $elements[] = sprintf('%s%s%s', $key, $withKeys, $value);
            } else {
                $elements[] = (string)$value;
            }
        }

        return implode($glue, $elements);
    }

    /**
     * Converts the collection to a JSON string
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->toJson();
    }

    /**
     * Returns the collection in a form suitable for encoding to JSON
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @link https://www.php.net/serialize
     *
     * @return string
     */
    public function serialize(): string
    {
        return serialize(['items' => $this->items]);
    }

    /**
     * @link https://www.php.net/unserialize
     *
     * @param string $serialized
     *
     * @return Collection|static
     */
    public function unserialize(string $serialized): Collection|static
    {
        $data = unserialize($serialized);

        if (is_array($data) && array_key_exists('items', $data)) {
            $this->items = $data['items'];
        }

        return $this;
    }
}
