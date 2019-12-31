<?php

namespace App\Domain\Model;

class Collection implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /** @var array Data associated with the object. */
    protected $data;

    /** @var int Position to allow looping */
    protected $position = 0;

    /**
     * @param array $data Associative array of data to set
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->position = 0;
    }

    public function __clone()
    {
        foreach ($this->data as $key => $object) {
            $this->data[$key] = clone $object;
        }
    }

    /**
     * @param Collection $collection1
     * @param Collection $collection2
     * @return Collection
     */
    public static function sum(Collection $collection1, Collection $collection2): self
    {
        return new self(array_merge($collection1->toArray(), $collection2->toArray()));
    }

    public function count(): int
    {
        return \count($this->data);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Removes all key value pairs
     *
     * @return Collection
     */
    public function clear(): self
    {
        $this->data = [];

        return $this;
    }

    /**
     * Get all or a subset of matching key value pairs
     *
     * @param array $keys Pass an array of keys to retrieve only a subset of key value pairs
     *
     * @return array Returns an array of all matching key value pairs
     */
    public function getAll(array $keys = null): array
    {
        return $keys
            ? array_intersect_key($this->data, array_flip($keys))
            : $this->data;
    }

    /**
     * Get a specific key value.
     *
     * @param string $key key to retrieve
     *
     * @return mixed|null Value of the key or NULL
     */
    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Set a key value pair
     *
     * @param string $key Key to set
     * @param mixed $value Value to set
     *
     * @return Collection Returns a reference to the object
     */
    public function set($key, $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Add a value to a key.  If a key of the same name has already been added, the key value will be converted into an
     * array and the new value will be pushed to the end of the array.
     *
     * @param string $key Key to add
     * @param mixed $value Value to add to the key
     *
     * @return Collection returns a reference to the object
     */
    public function add($key, $value): self
    {
        if (!array_key_exists($key, $this->data)) {
            $this->data[$key] = $value;
        } elseif (\is_array($this->data[$key])) {
            $this->data[$key][] = $value;
        } else {
            $this->data[$key] = [$this->data[$key], $value];
        }

        return $this;
    }

    /**
     * Remove a specific key value pair
     *
     * @param string $key A key to remove
     *
     * @return Collection
     */
    public function remove($key): self
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * Get all keys in the collection
     *
     * @return array
     */
    public function getKeys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Case insensitive search the keys in the collection
     *
     * @param string $key Key to search for
     *
     * @return bool|string Returns false if not found, otherwise returns the key
     */
    public function keySearch($key)
    {
        foreach (array_keys($this->data) as $k) {
            if (!strcasecmp($k, $key)) {
                return $k;
            }
        }

        return false;
    }

    /**
     * Checks if any keys contains a certain value
     *
     * @param string $value Value to search for
     *
     * @return mixed returns the key if the value was found FALSE if the value was not found
     */
    public function hasValue($value)
    {
        return array_search($value, $this->data, null);
    }

    /**
     * Replace the data of the object with the value of an array
     *
     * @param array $data Associative array of data
     *
     * @return Collection Returns a reference to the object
     */
    public function replace(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function current()
    {
        return $this->data[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->hasKey($this->position);
    }

    /**
     * Returns whether or not the specified key is present.
     *
     * @param string $key the key for which to check the existence
     *
     * @return bool
     */
    public function hasKey($key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function jsonSerialize()
    {
        return (array)$this->data;
    }

    /**
     * @param $field
     * @param $value
     * @return Collection (objects without the specified property)
     */
    public function removeBy($field, $value): Collection
    {
        $this->data = array_values(array_filter($this->data, function ($data) use ($field, $value) {
            $method = 'get' . ucwords($field);
            return $data->$method() !== $value;
        }));

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return Collection (objects with the specified property)
     */
    public function filterBy($field, $value): Collection
    {
        $returnObjets =  [];
        foreach ($this->data as $object) {
            $method = 'get' . ucwords($field);
            if (method_exists($object, $method) && $object->{$method}() === $value) {
                $returnObjets[] = $object;
            }
        }

        return new Collection($returnObjets);
    }

    /**
     * @return mixed|null
     */
    public function last()
    {
        return $this->get($this->count() - 1);
    }

    /**
     * @return Collection
     */
    public function resetKeys(): Collection
    {
        $this->data = array_values($this->data);

        return $this;
    }

    /**
     * @param callable $callable
     * @return Collection
     */
    public function applyFunction(callable $callable): Collection
    {
        $array = $this->data;

        $data = array_map($callable, $array);

        return new Collection($data);
    }

    /**
     * @param string $field
     * @return Collection
     * @throws \Exception
     */
    public function popCollectionField(string $field): Collection
    {
        $returnArray = [];

        $it = $this->getIterator();
        while ($it->valid()) {
            $object = $it->current();
            $method = 'get' . ucwords($field);
            if (method_exists($object, $method)) {
                $returnArray[] = $object->{$method}();
            }
            $it->next();
        }

        return new Collection($returnArray);
    }

    /**
     * @param int $start
     * @param int $length
     * @return Collection
     */
    public function slice(int $start, int $length = 0): self
    {
        if (!$length) {
            $length = $this->count();
        }

        return new self(
            \array_slice($this->toArray(), $start, $length)
        );
    }
}
