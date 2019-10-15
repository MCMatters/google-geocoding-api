<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding;

use InvalidArgumentException;
use McMatters\GoogleGeoCoding\Collections\AddressCollection;
use McMatters\GoogleGeoCoding\Components\HttpClient;

use function is_string, strpos;

use const null;

/**
 * Class GeoCoder
 *
 * @package McMatters\GoogleGeoCoding
 */
class GeoCoder
{
    /**
     * @var \McMatters\GoogleGeoCoding\Components\HttpClient
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
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($key)
    {
        $this->httpClient = new HttpClient($key);
    }

    /**
     * @param string|null $address
     * @param array $query
     *
     * @return \McMatters\GoogleGeoCoding\Collections\AddressCollection
     *
     * @throws \InvalidArgumentException
     * @throws \McMatters\GoogleGeoCoding\Exceptions\GeoCodingException
     * @throws \Throwable
     */
    public function getByAddress(
        string $address = null,
        array $query = []
    ): AddressCollection {
        if (null === $address && empty($query)) {
            throw new InvalidArgumentException(
                '"address" or "components" is required.'
            );
        }

        return $this->httpClient->get($query + ['address' => $address]);
    }

    /**
     * @param float|string $lat
     * @param float|string|null $lng
     * @param string|null $placeId
     * @param array $query
     *
     * @return AddressCollection
     *
     * @throws \InvalidArgumentException
     * @throws \McMatters\GoogleGeoCoding\Exceptions\GeoCodingException
     * @throws \Throwable
     */
    public function getByLatLng(
        $lat,
        $lng = null,
        string $placeId = null,
        array $query = []
    ): AddressCollection {
        if (is_string($lat) && strpos($lat, ',')) {
            $placeId = null !== $lng ? (string) $lng : null;
            $coordinates = $lat;
        } else {
            $coordinates = "{$lat},{$lng}";
        }

        return $this->httpClient->get(
            ['latlng' => $coordinates, 'place_id' => $placeId] + $query
        );
    }
}
