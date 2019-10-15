<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding\Models;

/**
 * Class Component
 *
 * @package McMatters\GoogleGeoCoding\Models
 */
class Component extends ItemModel
{
    /**
     * @var string
     */
    protected $longName;

    /**
     * @var string
     */
    protected $shortName;

    /**
     * @var array
     */
    protected $types;

    /**
     * Component constructor.
     *
     * @param array $item
     */
    public function __construct(array $item)
    {
        parent::__construct($item);

        $this->setLongName($item)
            ->setShortName($item)
            ->setTypes($item);
    }

    /**
     * @return string
     */
    public function getLongName(): string
    {
        return $this->longName;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param array $item
     *
     * @return self
     */
    protected function setLongName(array $item): self
    {
        $this->longName = $item['long_name'] ?? '';

        return $this;
    }

    /**
     * @param array $item
     *
     * @return self
     */
    protected function setShortName(array $item): self
    {
        $this->shortName = $item['short_name'] ?? '';

        return $this;
    }

    /**
     * @param array $item
     *
     * @return self
     */
    protected function setTypes(array $item): self
    {
        $this->types = $item['types'] ?? [];

        return $this;
    }
}
