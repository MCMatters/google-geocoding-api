<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Models;

use McMatters\GoogleGeocoding\Collections\ComponentCollection;
use const false;

/**
 * Class Address
 *
 * @package McMatters\GoogleGeocoding\Models
 */
class Address extends ItemModel
{
    /**
     * @var string
     */
    protected $formatted;

    /**
     * @var bool
     */
    protected $partialMatch;

    /**
     * @var string
     */
    protected $placeId;

    /**
     * @var array
     */
    protected $types;

    /**
     * @var ComponentCollection
     */
    protected $components;

    /**
     * @var Geometry
     */
    protected $geometry;

    /**
     * Address constructor.
     *
     * @param array $item
     */
    public function __construct(array $item)
    {
        parent::__construct($item);

        $this->setFormatted($item)
            ->setPartialMatch($item)
            ->setPlaceId($item)
            ->setTypes($item)
            ->setComponents($item)
            ->setGeometry($item);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return bool
     */
    public function isInViewport(float $latitude, float $longitude): bool
    {
        return $this->geometry->isInViewport($latitude, $longitude);
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->geometry->getLatitude();
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->geometry->getLongitude();
    }

    /**
     * @return string
     */
    public function getFormatted(): string
    {
        return $this->formatted;
    }

    /**
     * @return bool
     */
    public function isPartialMatch(): bool
    {
        return $this->partialMatch;
    }

    /**
     * @return string
     */
    public function getPlaceId(): string
    {
        return $this->placeId;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @return ComponentCollection
     */
    public function getComponents(): ComponentCollection
    {
        return $this->components;
    }

    /**
     * @return Geometry
     */
    public function getGeometry(): Geometry
    {
        return $this->geometry;
    }

    /**
     * @param array $item
     *
     * @return $this
     */
    protected function setFormatted(array $item): self
    {
        $this->formatted = $item['formatted_address'] ?? '';

        return $this;
    }

    /**
     * @param array $item
     *
     * @return $this
     */
    protected function setPartialMatch(array $item): self
    {
        $this->partialMatch = $item['partial_match'] ?? false;

        return $this;
    }

    /**
     * @param array $item
     *
     * @return $this
     */
    protected function setPlaceId(array $item): self
    {
        $this->placeId = $item['place_id'] ?? '';

        return $this;
    }

    /**
     * @param array $item
     *
     * @return $this
     */
    protected function setTypes(array $item): self
    {
        $this->types = $item['types'] ?? [];

        return $this;
    }

    /**
     * @param array $item
     *
     * @return $this
     */
    protected function setComponents(array $item): self
    {
        $this->components = new ComponentCollection(
            $item['address_components'] ?? []
        );

        return $this;
    }

    /**
     * @param array $item
     *
     * @return $this
     */
    protected function setGeometry(array $item): self
    {
        $this->geometry = new Geometry($item['geometry'] ?? []);

        return $this;
    }
}
