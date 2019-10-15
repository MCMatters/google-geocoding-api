<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding\Models;

/**
 * Class ItemModel
 *
 * @package McMatters\GoogleGeoCoding\Models
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
     *
     * @return void
     */
    protected function setRaw(array $item): void
    {
        $this->raw = $item;
    }
}
