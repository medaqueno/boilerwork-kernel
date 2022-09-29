#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Collection\Behaviours;

use Boilerwork\Support\Collection\Collection;
use Boilerwork\Support\Collection\Value;

trait Mutate
{

    /**
     * Append the value to the collection
     *
     * @param mixed $value
     *
     * @return Collection|static
     */
    public function add(mixed $value): Collection|static
    {
        $this->append($value);

        return $this;
    }

    /**
     * Add elements to the end of the collection
     *
     * @link https://www.php.net/array_push
     *
     * @param mixed ...$value One or values to add
     *
     * @return Collection|static
     */
    public function append(mixed ...$value): Collection|static
    {
        array_push($this->items, ...$value);

        return $this;
    }

    /**
     * Add elements to the end of the collection
     *
     * @link https://www.php.net/array_push
     *
     * @param mixed ...$value One or values to add
     *
     * @return Collection|static
     */
    public function appendUnique(mixed ...$value): Collection|static
    {
        foreach ($value as $item) {
            if ($this->contains($item)) {

                throw new \Exception(sprintf(
                    'The set already contains a value: "%s" at key "%s"',
                    $item,
                    // is_object($value) ? get_class($value) : gettype($value),
                    $this->keys($item)->first()
                ));
            }

            array_push($this->items, $item);
        }


        return $this;
    }

    /**
     * Create a collection by using this collection for keys and another for its values
     *
     * @link https://www.php.net/array_combine
     *
     * @param mixed $items
     *
     * @return Collection|static
     */
    public function combine(mixed $items): Collection|static
    {
        return $this->new(array_combine($this->items, Value::toArray($items)));
    }

    /**
     * Create a collection by using this collection for keys and another for its values
     *
     * @link https://www.php.net/array_combine
     *
     * @param mixed $items
     *
     * @return Collection|static
     */
    public function combineUnique(mixed $items): Collection|static
    {
        $items  = Value::toArray($items);
        $unique = array_unique($items);

        if (count($items) !== count($unique)) {
            throw new \Exception(
                'The set already contains a value'
            );
        }

        return $this->new(array_combine($this->items, $items));
    }

    /**
     * Merges the supplied array into the current Collection
     *
     * Note: should only be used with Collections of the same data, may cause strange results otherwise.
     * This method will re-index keys and overwrite existing values. If you wish to
     * preserve keys and values see {@link append}.
     *
     * @link https://www.php.net/array_merge
     *
     * @param mixed $value The value to merge into this collection
     *
     * @return Collection|static
     */
    public function merge(mixed $value): Collection|static
    {
        $this->items = array_merge($this->items, Value::toArray($value));

        return $this;
    }

    /**
     * Push all of the given items onto the collection.
     *
     * @param iterable $items
     *
     * @return Collection|static
     */
    public function concat(iterable $items): Collection|static
    {
        foreach ($items as $item) {
            $this->push($item);
        }

        return $this;
    }

    final public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    final public function offsetUnset($offset): void
    {
        if ($this->offsetExists($offset)) {
            $this->items[$offset] = null;
            unset($this->items[$offset]);
        }
    }

    final public function __set($offset, $value): void
    {
        $this->offsetSet($offset, $value);
    }

    final public function __unset($offset): void
    {
        $this->offsetUnset($offset);
    }

    /**
     * Clear all elements from the collection
     *
     * @return Collection|static
     */
    public function clear(): Collection|static
    {
        $this->items = [];

        return $this;
    }

    /**
     * Add the value at the specified key/offset to the collection
     *
     * @param int|string $key
     * @param mixed      $value
     *
     * @return Collection|static
     */
    public function set(int|string $key, mixed $value): Collection|static
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Fill an array with values beginning at index defined by start for count members
     *
     * Start can be a negative number. Count can be zero or more.
     *
     * @link https://www.php.net/array_fill
     *
     * @param int   $start
     * @param int   $count
     * @param mixed $value
     *
     * @return Collection|static
     */
    public function fill(int $start, int $count, mixed $value): Collection|static
    {
        return $this->new(array_fill($start, $count, $value));
    }

    /**
     * For all values in the current Collection, use as a key and assign $value to them
     *
     * This should only be used with scalar values that can be used as array keys.
     * A new Collection is returned with all previous values as keys, assigned the value.
     *
     * @link https://www.php.net/array_fill_keys
     *
     * @param mixed $value
     *
     * @return Collection|static
     */
    public function fillKeysWith(mixed $value): Collection|static
    {
        return $this->new(array_fill_keys($this->values()->toArray(), $value));
    }

    /**
     * Exchange all values for keys and return new Collection
     *
     * Note: this should only be used with elements that can be used as valid PHP array keys.
     *
     * @link https://www.php.net/array_flip
     *
     * @return Collection|static
     */
    public function flip(): Collection|static
    {
        return $this->new(array_flip($this->items));
    }


    /**
     * Remove the key from the collection
     *
     * @param int|string $key
     *
     * @return Collection|static
     */
    public function unset(int|string $key): Collection|static
    {
        $this->offsetUnset($key);

        return $this;
    }

    /**
     * Pops the element off the end of the Collection
     *
     * @link https://www.php.net/array_pop
     *
     * @return mixed
     */
    public function pop(): mixed
    {
        $value = array_pop($this->items);

        if (self::isArrayWrappingEnabled() && is_array($value)) {
            $value = $this->new($value);
        }

        return $value;
    }

    /**
     * Remove the value from the collection
     *
     * @param mixed $value
     *
     * @return Collection|static
     */
    public function remove(mixed $value): Collection|static
    {
        $this->keys($value)->each(fn ($key) => $this->offsetUnset($key));

        return $this;
    }

    /**
     * Pads the Collection to size using value as the value of the new elements
     *
     * @link https://www.php.net/array_pad
     *
     * @param integer $size
     * @param mixed   $value
     *
     * @return Collection|static
     */
    public function pad(int $size, mixed $value): Collection|static
    {
        $this->items = array_pad($this->items, $size, $value);

        return $this;
    }

    /**
     * Remove the first value from the collection
     *
     * @link https://www.php.net/array_shift
     *
     * @return mixed
     */
    public function shift(): mixed
    {
        $value = array_shift($this->items);

        if (self::isArrayWrappingEnabled() && is_array($value)) {
            $value = $this->new($value);
        }

        return $value;
    }

    /**
     * @link https://www.php.net/array_replace
     *
     * @param array ...$items
     *
     * @return Collection|static
     */
    public function replace(array ...$items): Collection|static
    {
        $this->items = array_replace($this->items, ...$items);

        return $this;
    }

    /**
     * @link https://www.php.net/array_replace_recursive
     *
     * @param array ...$items
     *
     * @return Collection|static
     */
    public function replaceRecursively(array ...$items): Collection|static
    {
        $this->items = array_replace_recursive($this->items, ...$items);

        return $this;
    }

    /**
     * Reverses the data in the Collection maintaining any keys
     *
     * @link https://www.php.net/array_reverse
     *
     * @return Collection|static
     */
    public function reverse(): Collection|static
    {
        $this->items = array_reverse($this->items, true);

        return $this;
    }

    /**
     * From the provided map of key -> new_key; change the current key to new_key
     *
     * The previous key is unset from the collection.
     *
     * @param array $map
     *
     * @return Collection|static
     */
    public function remapKeys(array $map): Collection|static
    {
        foreach ($this->items as $key => $value) {
            if (isset($map[$key])) {
                $this->items[$map[$key]] = $value;
                $this->offsetUnset($key);
            }
        }

        return $this;
    }

    /**
     * Prepends the elements to the beginning of the collection
     *
     * @link https://www.php.net/array_unshift
     *
     * @param mixed ...$value
     *
     * @return Collection|static
     */
    public function prepend(mixed ...$value): Collection|static
    {
        array_unshift($this->items, ...$value);

        return $this;
    }

    /**
     * Shuffle the items in the collection; does NOT return a new collection.
     *
     * @link https://www.php.net/shuffle
     *
     * @return Collection|static
     */
    public function shuffle(): Collection|static
    {
        shuffle($this->items);

        return $this;
    }

    /**
     * Shuffle the items in the collection; returning a new collection.
     *
     * @link https://www.php.net/shuffle
     *
     * @return Collection|static
     */
    public function shuffleIntoNewCollection(): Collection|static
    {
        $items = $this->items;

        shuffle($items);

        return $this->new($this);
    }


    /**
     * Shuffles the collection and picks the first element from it
     *
     * @return mixed
     */
    public function random(): mixed
    {
        return $this->shuffle()->first();
    }

    /**
     * When the given test passes run the then callable on the collection
     *
     * Note: the original collection is always returned and not the result of the callable.
     * The test can be a callable or a value that can evaluate to true/false.
     *
     * @param mixed         $test The test to check, can be a callable
     * @param callable      $then
     * @param callable|null $else
     *
     * @return static
     */
    public function when(mixed $test, callable $then, ?callable $else = null): static
    {
        if (is_callable($test)) {
            $test = $test($this);
        }

        $test ? $then($this) : (is_null($else) ?: $else($this));

        return $this;
    }
}
