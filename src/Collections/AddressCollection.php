<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Collections;

use McMatters\GoogleGeocoding\Models\Address;
use McMatters\GoogleGeocoding\Models\Geometry;
use const false, true;
use function array_merge, reset;

/**
 * Class AddressCollection
 *
 * @package McMatters\GoogleGeocoding\Collections
 */
class AddressCollection extends ItemCollection
{
    /**
     * @var string
     */
    protected $model = Address::class;

    /**
     * @return array
     */
    public function getFormattedNames(): array
    {
        return $this->pluck('getFormatted')->all();
    }

    /**
     * @return AddressCollection
     */
    public function getFullMatched(): self
    {
        return $this->getMatched();
    }

    /**
     * @return AddressCollection
     */
    public function getPartialMatched(): self
    {
        return $this->getMatched(true);
    }

    /**
     * @return ComponentCollection
     */
    public function getComponents(): ComponentCollection
    {
        $items = [];

        foreach ($this->pluck('getComponents') as $components) {
            $items[] = $components->all();
        }

        return new ComponentCollection(array_merge([], ...$items));
    }

    /**
     * @return Address|null
     */
    public function getExactMatch()
    {
        $addresses = [];

        if ($this->count() === 1) {
            return $this->first();
        }

        foreach ($this->items as $item) {
            if ($item->isPartialMatch() === false) {
                return $item;
            }

            $addresses[$item->getGeometry()->getType()][] = $item;
        }

        foreach (Geometry::getRelevancyTypes() as $type) {
            if (!empty($addresses[$type])) {
                return reset($addresses[$type]);
            }
        }

        return null;
    }

    /**
     * @param bool $flag
     *
     * @return AddressCollection
     */
    protected function getMatched(bool $flag = false): self
    {
        $items = [];

        /** @var Address $item */
        foreach ($this->items as $item) {
            if ($item->isPartialMatch() === $flag) {
                $items[] = $item;
            }
        }

        return new static($items);
    }
}
