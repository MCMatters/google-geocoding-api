<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Collections;

use McMatters\GoogleGeocoding\Models\Component;

/**
 * Class ComponentCollection
 *
 * @package McMatters\GoogleGeocoding\Collections
 */
class ComponentCollection extends ItemCollection
{
    /**
     * @var string
     */
    protected $model = Component::class;

    /**
     * @return array
     */
    public function getLongNames(): array
    {
        return $this->pluck('getLongName')->all();
    }

    /**
     * @return array
     */
    public function getShortNames(): array
    {
        return $this->pluck('getShortName')->all();
    }
}
