#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Support\Collection\Behaviours;

use Boilerwork\Support\Collection\Collection;
use Boilerwork\Support\Collection\Value;

trait Partition
{
    /**
     * Group the elements in the collection by the callable, returning a new collection
     *
     * The callable should return a valid key to group elements into. A valid key is
     * a string or integer or the current rules of PHP. Each group is a collection of
     * the values matched to it.
     *
     * @param callable $criteria
     *
     * @return Collection|static
     */
    public function groupBy(callable $criteria): Collection|static
    {
        $groups = [];

        foreach ($this->items as $key => $value) {
            $group = $criteria($value, $key);

            if (!isset($groups[$group])) {
                $groups[$group] = [];
            }

            $groups[$group][] = $value;
        }

        foreach ($groups as $group => $items) {
            $groups[$group] = $this->new($items);
        }

        return $this->new($groups);
    }

    /**
     * Extracts a portion of the Collection, returning a new Collection
     *
     * By default, preserves the keys.
     *
     * @link https://www.php.net/array_slice
     *
     * @param int      $offset
     * @param int|null $limit
     * @param bool     $keys
     *
     * @return Collection|static
     */
    public function slice(int $offset, int $limit = null, bool $keys = true): Collection|static
    {
        return $this->new(array_slice($this->items, $offset, $limit, $keys));
    }

    /**
     * Splice a portion of the underlying collection
     *
     * @link https://www.php.net/array_splice
     *
     * @param int      $offset
     * @param int|null $length
     * @param mixed    $replacement
     *
     * @return Collection|static
     */
    public function splice(int $offset, int $length = null, $replacement = []): Collection|static
    {
        if (func_num_args() === 1) {
            return $this->new(array_splice($this->items, $offset));
        }

        return $this->new(array_splice($this->items, $offset, $length, $replacement));
    }

    /**
     * Partition the Collection into two Collections using the given callback or key.
     *
     * Based on Laravel: Illuminate\Support\Collection.partition
     *
     * @param callable|string $callback
     *
     * @return Collection|static[static, static]
     */
    public function partition(string|callable $callback): Collection|static
    {
        $partitions = [[], []];
        $callback   = Value::accessor($callback);

        foreach ($this->items as $key => $item) {
            $partitions[(int) !$callback($item)][$key] = $item;
        }

        return $this->new([$this->new($partitions[0]), $this->new($partitions[1])]);
    }
}
