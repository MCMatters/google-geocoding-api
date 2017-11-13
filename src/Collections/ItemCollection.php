<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Collections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use McMatters\GoogleGeocoding\Models\ItemModel;
use Traversable;
use const null;
use function array_key_exists, array_unique, count, is_array, is_callable,
    is_object, iterator_to_array;

/**
 * Class ItemCollection
 *
 * @package McMatters\GoogleGeocoding\Collections
 */
class ItemCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var array
     */
    protected $items;

    /**
     * ItemCollection constructor.
     *
     * @param array $items
     */
    public function __construct($items)
    {
        $this->setItems($items);
    }

    /**
     * @return string|null
     */
    public function getModel()
    {
        return $this->model ?? null;
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        foreach ($this->items as $item) {
            return $item;
        }

        return null;
    }

    /**
     * @return ItemCollection
     */
    public function unique(): self
    {
        return new self(array_unique($this->items));
    }

    /**
     * @param string $valueKey
     * @param string|int|null $key
     *
     * @return ItemCollection
     */
    public function pluck(string $valueKey, string $key = null): self
    {
        $items = [];
        $i = 0;

        foreach ($this->items as $itemKey => $item) {
            $key = $key ?? $i;

            $isObject = is_object($item);

            if (is_array($item) && array_key_exists($valueKey, $item)) {
                $items[$key] = $item[$valueKey];
            } elseif ($isObject && isset($item->{$valueKey})) {
                $items[$key] = $item->{$valueKey};
            } elseif ($isObject && is_callable([$item, $valueKey])) {
                $items[$key] = $item->{$valueKey}();
            } else {
                $items[$key] = null;
            }

            $i++;
        }

        return new self($items);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param mixed $key
     *
     * @return ItemModel|null
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @return array
     */
    public function raw(): array
    {
        $raw = [];

        foreach ($this->items as $key => $item) {
            if ($item instanceof self) {
                $raw[$key] = $item->raw();
            } elseif ($item instanceof ItemModel) {
                $raw[$key] = $item->getRaw();
            } else {
                $raw[$key] = $item;
            }
        }

        return $raw;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return ItemModel|null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->items[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->items[$offset]);
        }
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param $items
     *
     * @return void
     */
    protected function setItems($items)
    {
        $items = $this->getArrayableItems($items);

        if (isset($this->model)) {
            foreach ($items as &$item) {
                if (!$item instanceof $this->model) {
                    $item = new $this->model($item);
                }
            }

            unset($item);
        }

        $this->items = $items;
    }

    /**
     * @param $items
     *
     * @return array
     */
    protected function getArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        }

        if ($items instanceof self) {
            return $items->all();
        }

        if ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array) $items;
    }
}
