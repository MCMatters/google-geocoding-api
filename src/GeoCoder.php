<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding;

use InvalidArgumentException;
use McMatters\GoogleGeocoding\Collections\AddressCollection;
use McMatters\GoogleGeocoding\Components\HttpClient;
use McMatters\GoogleGeocoding\Components\UrlBuilder;
use McMatters\GoogleGeocoding\Exceptions\GeoCodingException;
use Throwable;
use const null, true;
use function array_merge, is_string, strpos;

/**
 * Class GeoCoder
 *
 * @package McMatters\GoogleGeocoding
 */
class GeoCoder
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var bool
     */
    protected $secure;

    /**
     * GeoCoder constructor.
     *
     * @param string|array $key
     * @param bool $secure
     *
     * @throws InvalidArgumentException
     */
    public function __construct($key, bool $secure = true)
    {
        $this->secure = $secure;
        $this->httpClient = new HttpClient($key);
    }

    /**
     * @param string|null $address
     * @param array|string $components
     * @param array $params
     *
     * @return AddressCollection
     * @throws InvalidArgumentException
     * @throws GeoCodingException
     * @throws Throwable
     */
    public function getByAddress(
        string $address = null,
        $components = [],
        array $params = []
    ): AddressCollection {
        if (null === $address && empty($components)) {
            throw new InvalidArgumentException(
                '$address or $components is required.'
            );
        }

        $params = array_merge($params, ['address' => $address]);

        return $this->httpClient->get(
            (new UrlBuilder($params, $components, $this->secure))->buildUrl()
        );
    }

    /**
     * @param float|string $lat
     * @param float|string|null $lng
     * @param string|null $placeId
     *
     * @return AddressCollection
     * @throws InvalidArgumentException
     * @throws GeoCodingException
     * @throws Throwable
     */
    public function getByLatLng(
        $lat,
        $lng = null,
        string $placeId = null
    ): AddressCollection {
        if (is_string($lat) && strpos($lat, ',')) {
            $placeId = null !== $lng ? (string) $lng : null;
            $coordinates = $lat;
        } else {
            $coordinates = "{$lat},{$lng}";
        }

        $params = ['latlng' => $coordinates, 'place_id' => $placeId];

        return $this->httpClient->get(
            (new UrlBuilder($params, [], $this->secure))->buildUrl()
        );
    }
}
