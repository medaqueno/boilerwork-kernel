<?php

declare(strict_types=1);

namespace Boilerwork\Helpers;

class Collection implements \ArrayAccess
{
    public function __construct(protected mixed $data)
    {
        if ($data instanceof Collection) {
            $this->data = $data->get();
        } else {
            $this->data = $data;
        }
    }

    public function has(mixed $key): bool
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $id = &$this->data;
            foreach ($keys as $key) {
                if (isset($id[$key])) {
                    $id = &$id[$key];
                } else {
                    return false;
                }
            }
            return true;
        }
        return isset($this->data[$key]);
    }

    public function get(mixed $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->data;
        }
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $id = &$this->data;
            foreach ($keys as $key) {
                if (isset($id[$key])) {
                    $id = &$id[$key];
                } else {
                    return null;
                }
            }
            return $id;
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return $default;
    }

    public function set(mixed $key, mixed $value): void
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $id = &$this->data;
            foreach ($keys as $key) {
                if (isset($id[$key])) {
                    $id = &$id[$key];
                } else {
                    return;
                }
            }
            $id = $value;
        } else {
            $this->data[$key] = $value;
        }
    }

    public function first(): mixed
    {
        if (isset($this->data[0])) {
            return $this->data[0];
        }
        return null;
    }

    public function filter(callable $call): mixed
    {
        $result = [];
        foreach ($this->data as $k => $v) {
            if ($call($v, $k)) {
                $result[] = $v;
            }
        }
        return $result;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
