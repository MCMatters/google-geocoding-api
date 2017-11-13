<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Components;

use InvalidArgumentException;
use McMatters\GoogleGeocoding\Exceptions\UrlLengthExceededException;
use const false, true;
use function array_filter, gettype, http_build_query, implode, mb_strlen, strpos;

/**
 * Class UrlBuilder
 *
 * @package McMatters\GoogleGeocoding\Components
 */
class UrlBuilder
{
    const URL_MAX_LENGTH = 8192;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var array|string
     */
    protected $components;

    /**
     * UrlBuilder constructor.
     *
     * @param array $params
     * @param array|string $components
     * @param bool $secure
     */
    public function __construct(
        array $params,
        $components = [],
        bool $secure = true
    ) {
        $this->setBaseUrl($secure);
        $this->params = $params;
        $this->components = $components;
    }

    /**
     * @return string
     * @throws UrlLengthExceededException
     * @throws InvalidArgumentException
     */
    public function buildUrl(): string
    {
        $query[] = $this->buildComponents();
        $query[] = http_build_query($this->params);

        $queries = implode('&', array_filter($query));

        $url = "{$this->baseUrl}?{$queries}";

        $this->checkUrlLength($url);

        return $url;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    protected function buildComponents(): string
    {
        if (empty($this->components)) {
            return '';
        }

        switch (gettype($this->components)) {
            case 'string':
                return $this->buildStringComponents();

            case 'array':
                return $this->buildArrayComponents();
        }

        throw new InvalidArgumentException('$components must be string or array');
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    protected function buildStringComponents(): string
    {
        if (strpos($this->components, ':') === false) {
            throw new InvalidArgumentException(
                '$components must be valid string or array'
            );
        }

        return strpos($this->components, 'components=') === 0
            ? $this->components
            : "components={$this->components}";
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    protected function buildArrayComponents(): string
    {
        $build = [];

        foreach ((array) $this->components as $key => $component) {
            $build[] = "{$key}:{$component}";
        }

        return !empty($build) ? 'components='.implode('|', $build) : '';
    }

    /**
     * @param bool $secure
     *
     * @return void
     */
    protected function setBaseUrl(bool $secure = true)
    {
        $scheme = $secure ? 'https' : 'http';

        $this->baseUrl = "{$scheme}://maps.googleapis.com/maps/api/geocode/json";
    }

    /**
     * @param string $url
     *
     * @throws UrlLengthExceededException
     */
    protected function checkUrlLength(string $url)
    {
        if (mb_strlen($url) > self::URL_MAX_LENGTH) {
            throw new UrlLengthExceededException();
        }
    }
}
