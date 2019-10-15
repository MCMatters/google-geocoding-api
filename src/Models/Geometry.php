<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding\Models;

/**
 * Class Geometry
 *
 * @package McMatters\GoogleGeoCoding\Models
 */
class Geometry extends ItemModel
{
    public const TYPE_APPROXIMATE = 'APPROXIMATE';
    public const TYPE_GEOMETRIC_CENTER = 'GEOMETRIC_CENTER';
    public const TYPE_RANGE_INTERPOLATED = 'RANGE_INTERPOLATED';
    public const TYPE_ROOFTOP = 'ROOFTOP';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    /**
     * @var array
     */
    protected $bounds;

    /**
     * @var array
     */
    protected $viewport;

    /**
     * Geometry constructor.
     *
     * @param array $item
     */
    public function __construct(array $item)
    {
        parent::__construct($item);

        $this->setType($item)
            ->setLatitude($item)
            ->setLongitude($item)
            ->setBounds($item)
            ->setViewport($item);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return bool
     */
    public function isInViewport(float $latitude, float $longitude): bool
    {
        return $latitude >= $this->viewport['southwest']['lat'] &&
            $latitude <= $this->viewport['northeast']['lat'] &&
            $longitude >= $this->viewport['southwest']['lng'] &&
            $longitude <= $this->viewport['northeast']['lng'];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return array
     */
    public function getLocation(): array
    {
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude,
        ];
    }

    /**
     * @return array
     */
    public function getBounds(): array
    {
        return $this->bounds;
    }

    /**
     * @return array
     */
    public function getViewport(): array
    {
        return $this->viewport;
    }

    /**
     * @return array
     */
    public static function getRelevancyTypes(): array
    {
        return [
            self::TYPE_ROOFTOP,
            self::TYPE_RANGE_INTERPOLATED,
            self::TYPE_GEOMETRIC_CENTER,
            self::TYPE_APPROXIMATE,
        ];
    }

    /**
     * It doesn't matter in which order it will be returned.
     * It is just an array of types.
     *
     * @return array
     */
    public static function getTypes(): array
    {
        return self::getRelevancyTypes();
    }

    /**
     * @param array $item
     *
     * @return self
     */
    protected function setType(array $item): self
    {
        $this->type = $item['location_type'] ?? '';

        return $this;
    }

    /**
     * @param array $item
     *
     * @return self
     */
    protected function setLatitude(array $item): self
    {
        $this->latitude = $item['location']['lat'] ?? 0.0;

        return $this;
    }

    /**
     * @param array $item
     *
     * @return self
     */
    protected function setLongitude(array $item): self
    {
        $this->longitude = $item['location']['lng'] ?? 0.0;

        return $this;
    }

    /**
     * @param array $item
     *
     * @return self
     */
    protected function setBounds(array $item): self
    {
        $this->bounds = $item['bounds'] ?? [];

        return $this;
    }

    /**
     * @param array $item
     *
     * @return self
     */
    protected function setViewport(array $item): self
    {
        $this->viewport = $item['viewport'] ?? [];

        return $this;
    }
}
