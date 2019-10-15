<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding\Collections;

use McMatters\GoogleGeoCoding\Models\Address;
use McMatters\GoogleGeoCoding\Models\Geometry;

use function array_merge, reset;

use const false, true;

/**
 * Class AddressCollection
 *
 * @package McMatters\GoogleGeoCoding\Collections
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
     * @return self
     */
    public function getFullMatched(): self
    {
        return $this->getMatched();
    }

    /**
     * @return self
     */
    public function getPartialMatched(): self
    {
        return $this->getMatched(true);
    }

    /**
     * @return \McMatters\GoogleGeoCoding\Collections\ComponentCollection
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
     * @return \McMatters\GoogleGeoCoding\Models\Address|null
     */
    public function getExactMatch(): ?Address
    {
        $addresses = [];

        if ($this->count() === 1) {
            return $this->first();
        }

        /** @var \McMatters\GoogleGeoCoding\Models\Address $item */
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
     * @return self
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
