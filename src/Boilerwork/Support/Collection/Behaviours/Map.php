#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Collection\Behaviours;

use Boilerwork\Support\Collection\Collection;
use Boilerwork\Support\Collection\Value;

trait Map
{

    /**
     * Collapse the collection of items into a single array
     *
     * @return Collection|static
     */
    public function collapse(): Collection|static
    {
        return $this->new(Value::collapse($this->items));
    }

    /**
     * Apply the callback to all elements in the collection
     *
     * Note: the callable should accept 2 arguments, the value and the key. For single
     * argument callables only the value will be passed in. The argument count of the
     * callable will attempt to be found. This works on methods, functions and static
     * callable (Class::method).
     *
     * @link https://www.php.net/array_map
     * @link https://github.com/laravel/framework/blob/5.8/src/Illuminate/Support/Collection.php#L1116
     *
     * @param callable|string $callable A callable or string name of a function
     *
     * @return Collection|static
     */
    public function map(string|callable $callable): Collection|static
    {
        if (1 === Value::getArgumentCountForCallable($callable)) {
            $callable = fn ($value, $key) => $callable($value);
        }

        $keys  = array_keys($this->items);
        $items = array_map($callable, $this->items, $keys);

        return $this->new(array_combine($keys, $items));
    }

    /**
     * Map a collection and flatten the result by a single level
     *
     * @link https://github.com/laravel/framework/blob/5.8/src/Illuminate/Support/Collection.php#L1213
     *
     * @param callable $callable
     *
     * @return Collection|static
     */
    public function flatMap(callable $callable): Collection|static
    {
        return $this->map($callable)->collapse();
    }

    /**
     * Map the values into a new class.
     *
     * @link https://github.com/laravel/framework/blob/5.8/src/Illuminate/Support/Collection.php#L1224
     *
     * @param string $class
     *
     * @return Collection|static
     */
    public function mapInto(string $class): Collection|static
    {
        return $this->map(fn ($value, $key) => new $class($value, $key));
    }

    /**
     * Reduces the Collection to a single value, returning it, or $initial if no value
     *
     * @link https://www.php.net/array_reduce
     *
     * @param callable $callback Receives mixed $carry, mixed $value
     * @param mixed    $initial  (optional) Default value to return if no result
     *
     * @return mixed
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Returns a new Collection with all sub-sets / arrays merged into one Collection
     *
     * If similar keys exist, they will be overwritten. This method is
     * intended to convert a multi-dimensional array into a key => value
     * array. This method is called recursively through the Collection.
     *
     * @return Collection|static
     */
    public function flatten(): Collection|static
    {
        return $this->new(Value::flatten($this->items));
    }

    /**
     * Returns a new Collection with all sub-sets / arrays merged into one Collection
     *
     * Key names are flattened into dot notation, though overwrites may still occur.
     *
     * @return Collection|static
     */
    public function flattenWithDotKeys(): Collection|static
    {
        return $this->new(Value::flatten($this->items, true));
    }
}
