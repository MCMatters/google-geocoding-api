<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding\Collections;

use McMatters\GoogleGeoCoding\Models\Component;

/**
 * Class ComponentCollection
 *
 * @package McMatters\GoogleGeoCoding\Collections
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
