#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Collection\Behaviours;

use Boilerwork\Support\Collection\Collection;
use Boilerwork\Support\Collection\Value;
use Boilerwork\System\Collection\Behaviours\BadMethodCallException;
use Boilerwork\System\Collection\Behaviours\RuntimeException;

trait Pipe
{

    /**
     * Pass the collection to the given callback and return the result
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public function pipe(callable $callback): mixed
    {
        return $callback($this);
    }

    /**
     * Transform a passed Collection of items using an Operator method
     *
     * Given a set of Operators that all implement the same interface, pass the Collection of
     * items to each Operator, calling a method on the operator that will transform each item
     * in the items Collection, creating a new Collection that is passed to subsequent Operators.
     *
     * In other words, if the Collection contains e.g. decorators that will add / modify an entity,
     * then `pipeline` will pass each item in turn through each decorator. Each time a new Collection
     * is built from the output of the previous decorator. This allows chaining the decorator calls.
     *
     * A method name for the Operator can be used as the second argument, otherwise a callable
     * must be provided that is passed: the operator object, an item from the items iterable
     * and the key. The callable should return the transformed item. The created Collection
     * preserves the keys, hence order, of the original items.
     *
     * This method can be used to modify a set of read-only objects via a series of independent,
     * but linked transformations. This is similar to the pipeline pattern, except it works on a
     * Collection instead of a single item.
     *
     * @param iterable        $items
     * @param string|callable $through Method name to call on the operator, or a closure
     *
     * @return Collection|static
     */
    public function pipeline(iterable $items, string|callable $through): Collection|static
    {
        foreach ($this->items as $key => $operator) {
            $new = [];

            if (!Value::isCallable($through)) {
                $through = fn ($operator, $item, $key) => $operator->{$through}($item);
            }

            foreach ($items as $k => $item) {
                $new[$k] = $through($operator, $item, $k);
            }

            $items = $new;
        }

        return $this->new($items);
    }

    /**
     * Execute a callback over the collection, halting if the callback returns false
     *
     * @param callable $callback Receives: ($value, $key)
     *
     * @return Collection|static
     */
    public function each(callable $callback): Collection|static
    {
        foreach ($this->items as $key => $value) {
            if (false === $callback($value, $key)) {
                break;
            }
        }

        return $this;
    }

    /**
     * Run the method or Closure on all object items in the collection
     *
     * If a closure is passed the current value, key and the unpacked arguments are provided.
     * The method is passed: the unpacked arguments
     *
     * run() can only be used with a Collection that contains objects or when the method is a
     * Closure. If a non-object type is encountered an Exception will be raised.
     *
     * @param string|callable $method
     * @param mixed           ...$arguments
     *
     * @return Collection|static
     * @throws RuntimeException
     * @throws BadMethodCallException
     */
    public function run(string|callable $method, mixed ...$arguments): Collection|static
    {
        foreach ($this->items as $key => $value) {
            if ($method instanceof \Closure) {
                $method($value, $key, ...$arguments);
                continue;
            }

            if (!is_object($value)) {
                throw new \RuntimeException(sprintf(
                    'Value is "%s" and "%s" cannot be called on it. Ensure collection only contains objects',
                    gettype($value),
                    $method
                ));
            }

            if (!method_exists($value, $method)) {
                throw new \BadMethodCallException(sprintf('Method "%s" not found on object "%s"', $method, get_class($value)));
            }

            $value->{$method}(...$arguments);
        }

        return $this;
    }
}
