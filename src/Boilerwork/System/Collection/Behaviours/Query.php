#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\System\Collection\Behaviours;

use Boilerwork\System\Collection\Collection;
use Boilerwork\System\Collection\KeyWalker;
use Boilerwork\System\Collection\Value;

trait Query
{
    /**
     * Filters the collection using the callback
     *
     * The callback receives both the value and the key. If a key name and value are given,
     * will filter all items at that key with the value provided. Key can be an object method,
     * property or array key.
     *
     * @link https://www.php.net/array_filter
     *
     * @param mixed $criteria PHP callable, closure or function, or property name to filter on
     * @param mixed $test The value to filter for
     *
     * @return Collection|static
     */
    public function filter(string|callable $criteria = null, mixed $test = null): Collection|static
    {
        if ($criteria && $test) {
            $criteria = fn ($value, $key) => KeyWalker::get($value, $criteria) === $test;
        }

        return $this->new(array_filter($this->items, $criteria, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Returns a new collection containing all values whose keys match the regex rule
     *
     * @param string $rule
     *
     * @return Collection|$this
     */
    public function matchingRule(string $rule): Collection|static
    {
        return $this->filter(fn ($value, $key) => 1 === preg_match($rule, $key));
    }

    /**
     * Alias of filter but requires the callable
     *
     * @param callable $criteria
     *
     * @return Collection|static
     */
    public function matching(callable $criteria): Collection|static
    {
        return $this->filter($criteria);
    }

    /**
     * Returns items that do NOT pass the test callable
     *
     * The callable is wrapped and checked if it returns false. For example: your callable is a closure
     * that `return Str::contains($value->name(), 'bob');`, then `notMatching` will return all items
     * that do not match that criteria.
     *
     * @param callable $criteria
     *
     * @return Collection|static
     */
    public function notMatching(callable $criteria): Collection|static
    {
        return $this->filter(fn ($value, $key) => !$criteria($value, $key));
    }

    /**
     * Alias of notMatching
     *
     * @param callable $criteria
     *
     * @return Collection|static
     */
    public function reject(callable $criteria): Collection|static
    {
        return $this->notMatching($criteria);
    }

    public function all(): array
    {
        return $this->items;
    }

    /**
     * Returns true if value is in the collection
     *
     * @link https://www.php.net/in_array
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function contains(mixed $value): bool
    {
        return in_array($value, $this->items, true);
    }

    public function doesNotContain($value): bool
    {
        return !$this->contains($value);
    }

    /**
     * Find keys matching the criteria, returning a new collection of the keys
     *
     * @param string|callable $criteria Regular expression or a closure
     *
     * @return Collection|static
     */
    public function keysMatching(string|callable $criteria): Collection|static
    {
        $matches = [];

        if (!Value::isCallable($criteria)) {
            $criteria = fn ($key) => 1 === preg_match($criteria, $key);
        }

        foreach ($this->keys() as $key) {
            if (true === Value::get($criteria, $key)) {
                $matches[] = $key;
            }
        }

        return $this->new($matches);
    }

    /**
     * Returns a new collection with only the specified keys
     *
     * @param string ...$keys
     *
     * @return Collection|static
     */
    public function with(int|string ...$keys): Collection|static
    {
        return $this->filter(function ($value, $key) use ($keys) {
            return in_array($key, $keys, true);
        });
    }

    /**
     * Returns a new collection WITHOUT the specified keys
     *
     * @param string ...$keys
     *
     * @return Collection|static
     */
    public function without(int|string ...$keys): Collection|static
    {
        return $this->filter(function ($value, $key) use ($keys) {
            return !in_array($key, $keys, true);
        });
    }

    /**
     * Returns true if any of the keys are present in the collection
     *
     * @param string ...$key
     *
     * @return bool
     */
    public function hasAnyOf(int|string ...$key): bool
    {
        foreach ($key as $test) {
            if ($this->has($test)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if NONE of the keys are present in the collection
     *
     * @param string ...$key
     *
     * @return bool
     */
    public function hasNoneOf(int|string ...$key): bool
    {
        $result = true;

        foreach ($key as $test) {
            $result = $result && !$this->has($test);
        }

        return $result;
    }

    /**
     * Alias for has(...$key)
     *
     * @param string ...$key
     *
     * @return bool
     */
    public function hasAllOf(int|string ...$key): bool
    {
        return $this->has(...$key);
    }

    /**
     * Finds the first item matching the criteria
     *
     * @param callable|mixed $criteria A callable or an element to match
     *
     * @return mixed
     */
    public function find(string|callable $criteria): mixed
    {
        if (!is_callable($criteria)) {
            $criteria = function ($value, $key) use ($criteria) {
                return $value === $criteria;
            };
        }

        return $this->filter($criteria)->first() ?? false;
    }

    /**
     * Finds the last item matching the criteria
     *
     * @param callable|mixed $criteria A callable or an element to match
     *
     * @return mixed
     */
    public function findLast(string|callable $criteria): mixed
    {
        return $this->filter($criteria)->last() ?? false;
    }

    /**
     * Returns the first element from the collection; or null if empty
     *
     * @return mixed
     */
    public function first(): mixed
    {
        $value = reset($this->items) ?: null;

        if (self::isArrayWrappingEnabled() && is_array($value)) {
            $value = $this->items[array_search($value, $this->items)] = $this->new($value);
        }

        return $value;
    }

    /**
     * Get the value at the specified key, if the _KEY_ does NOT exist, return the default
     *
     * Note: if the key is null or false, the value will be returned. If you must have a non
     * falsey value, use {@link value()} instead.
     *
     * @param int|string $key
     * @param mixed      $default
     *
     * @return mixed
     */
    public function get(int|string $key, mixed $default = null): mixed
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return $default;
    }

    /**
     * Returns true if the key(s) all exist in the collection
     *
     * @param string ...$key
     *
     * @return bool
     */
    public function has(int|string ...$key): bool
    {
        $result = true;

        foreach ($key as $test) {
            $result = $result && $this->offsetExists($test);
        }

        return $result;
    }

    /**
     * Returns true if the specified key exists in the Collection and is not empty
     *
     * Empty in this case is not an empty string, null, zero or false. It should not
     * be used to check for null or boolean values.
     *
     * @param int|string $key
     *
     * @return boolean
     */
    public function hasValueFor(int|string $key): bool
    {
        return ($this->has($key) && $this->get($key));
    }

    /**
     * Returns a new collection containing just the keys as values
     *
     * If a value is provided, then all keys with this value will be returned. Searching
     * is always by strict match.
     *
     * @link https://www.php.net/array_keys
     *
     * @param mixed $value Get all keys where the value matches
     *
     * @return Collection|static
     */
    public function keys(mixed $value = null): Collection|static
    {
        if (null === $value) {
            return $this->new(array_keys($this->items));
        }

        return $this->new(array_keys($this->items, $value, true));
    }

    /**
     * Returns the last element of the Collection or null if empty
     *
     * @return mixed
     */
    public function last(): mixed
    {
        $value = end($this->items) ?: null;

        if (self::isArrayWrappingEnabled() && is_array($value)) {
            $value = $this->items[array_search($value, $this->items)] = $this->new($value);
        }

        return $value;
    }

    /**
     * Returns the value for the specified key
     *
     *
     * @param int|string     $key
     * @param mixed
     *
     * @return mixed
     */
    public function value(int|string $key, mixed $default = null): mixed
    {
        if ($value = $this->get($key)) {
            return $value;
        }

        return null;
    }

    /**
     * Returns a new collection containing just the values without the previous keys
     *
     * @return Collection|static
     */
    public function values(): Collection|static
    {
        return $this->new(array_values($this->items));
    }

    /**
     * Creates a new Collection containing only unique values
     *
     * @link https://www.php.net/array_unique
     *
     * @param integer $type Sort flags to use on values, default SORT_STRING
     *
     * @return Collection|static
     */
    public function unique(int $type = SORT_STRING): Collection|static
    {
        return $this->new(array_unique($this->items, $type));
    }


    /**
     * Removes values that are matched as empty through an equivalence check
     *
     * @param array $empty Array of values considered to be "empty"
     *
     * @return Collection|static
     */
    public function removeEmpty(array $empty = [false, null, '']): Collection|static
    {
        return $this->filter(fn ($item) => !in_array($item, $empty, true));
    }

    /**
     * Removes any null items from the Collection, returning a new collection
     *
     * @return Collection|static
     */
    public function removeNulls(): Collection|static
    {
        return $this->filter(fn ($item) => !is_null($item));
    }

    /**
     * Sort the Collection by a user defined function, preserves key association
     *
     * @link https://www.php.net/uasort
     *
     * @param string|callable $callable Any valid PHP callable e.g. function, closure, method
     *
     * @return Collection|static
     */
    public function sort(string|callable $callable): Collection|static
    {
        if (!is_callable($callable)) {
            $callable = fn ($a, $b) => KeyWalker::get($a, $callable) <=> KeyWalker::get($b, $callable);
        }

        uasort($this->items, $callable);

        return $this;
    }

    /**
     * Sort the collection by `value` or `key` ordered `asc` (A-Z) or `desc` (Z-A)
     *
     * @link https://www.php.net/asort
     * @link https://www.php.net/arsort
     * @link https://www.php.net/ksort
     * @link https://www.php.net/krsort
     *
     * @param string $type Either values or keys, default values
     * @param string $dir  Either asc or desc, default asc
     * @param int    $comparison One of the SORT_ constants, default being SORT_STRING
     *
     * @return Collection|static
     */
    public function sortBy(string $type, string $dir = 'asc', int $comparison = SORT_STRING): Collection|static
    {
        $fn = $type === 'key' && $dir === 'desc' ? 'krsort' : ($type === 'key' ? 'ksort' : ($dir === 'desc' ? 'arsort' : 'asort'));

        $fn($this->items, $comparison);

        return $this;
    }
}
