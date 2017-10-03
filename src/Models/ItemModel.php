<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Models;

/**
 * Class ItemModel
 *
 * @package McMatters\GoogleGeocoding\Models
 */
abstract class ItemModel
{
    /**
     * @var array
     */
    protected $raw;

    /**
     * ItemModel constructor.
     *
     * @param array $item
     */
    public function __construct(array $item)
    {
        $this->setRaw($item);
    }

    /**
     * @return array
     */
    public function getRaw(): array
    {
        return $this->raw;
    }

    /**
     * @param array $item
     */
    protected function setRaw(array $item)
    {
        $this->raw = $item;
    }
}
