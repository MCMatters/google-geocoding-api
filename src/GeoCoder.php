<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use McMatters\GoogleGeocoding\Collections\AddressCollection;
use McMatters\GoogleGeocoding\Exceptions\{
    InvalidRequestException, QuotaLimitExceededException,
    RequestDeniedException, UnknownErrorException, UrlLengthExceededException
};
use RuntimeException;
use const true;
use function array_filter, array_key_exists, array_merge, gettype,
    http_build_query, implode, mb_strlen, strpos;

/**
 * Class GeoCoder
 *
 * @package McMatters\GoogleGeocoding
 */
class GeoCoder
{
    const URL_MAX_LENGTH = 8192;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * GeoCoder constructor.
     *
     * @param string $apiKey
     * @param bool $secure
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $apiKey, bool $secure = true)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = new Client();
        $this->setBaseUrl($secure);
    }

    /**
     * @param string|null $address
     * @param array $components
     * @param array $params
     *
     * @return AddressCollection
     * @throws UnknownErrorException
     * @throws RequestDeniedException
     * @throws QuotaLimitExceededException
     * @throws InvalidRequestException
     * @throws RuntimeException
     * @throws UrlLengthExceededException
     * @throws ClientException
     * @throws InvalidArgumentException
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

        $response = $this->request(
            $this->buildUrl(
                array_merge($params, ['address' => $address]),
                $components
            )
        );

        return $this->parseResponse($response);
    }

    /**
     * @param string $lat
     * @param string|null $lng
     * @param string|null $placeId
     *
     * @return AddressCollection
     * @throws UnknownErrorException
     * @throws RequestDeniedException
     * @throws QuotaLimitExceededException
     * @throws InvalidRequestException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws UrlLengthExceededException
     * @throws ClientException
     */
    public function getByLatLng(
        string $lat,
        string $lng = null,
        string $placeId = null
    ): AddressCollection {
        if (strpos($lat, ',')) {
            $placeId = $lng;
            $coordinates = $lat;
        } else {
            $coordinates = "{$lat},$lng";
        }

        $response = $this->request(
            $this->buildUrl(['latlng' => $coordinates, 'place_id' => $placeId])
        );

        return $this->parseResponse($response);
    }

    /**
     * @param array $params
     * @param array $components
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function buildUrl(array $params, $components = []): string
    {
        $params['key'] = $this->apiKey;

        $query[] = $this->buildComponents($components);
        $query[] = http_build_query($params);

        $queries = implode('&', array_filter($query));

        return "{$this->baseUrl}?{$queries}";
    }

    /**
     * @param string|array $components
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function buildComponents($components = []): string
    {
        if (empty($components)) {
            return '';
        }

        switch (gettype($components)) {
            case 'string':
                return $this->buildStringComponents($components);

            case 'array':
                return $this->buildArrayComponents($components);
        }

        throw new InvalidArgumentException('$components must be string or array');
    }

    /**
     * @param string $components
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function buildStringComponents(string $components): string
    {
        if (strpos($components, ':') === false) {
            throw new InvalidArgumentException(
                '$components must be valid string or array'
            );
        }

        return strpos($components, 'components=') === 0
            ? $components
            : "components={$components}";
    }

    /**
     * @param array $components
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function buildArrayComponents(array $components): string
    {
        $build = [];

        foreach ($components as $key => $component) {
            $build[] = "{$key}:{$component}";
        }

        return !empty($build) ? 'components='.implode('|', $build) : '';
    }

    /**
     * @param string $url
     *
     * @return string
     * @throws RuntimeException
     * @throws ClientException
     * @throws UrlLengthExceededException
     */
    protected function request(string $url)
    {
        $this->checkUrlLength($url);

        try {
            return $this->httpClient->get($url)->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if (null === $response) {
                throw $e;
            }

            return $response->getBody()->getContents();
        }
    }

    /**
     * @param string|null $response
     *
     * @return AddressCollection
     * @throws UnknownErrorException
     * @throws RequestDeniedException
     * @throws QuotaLimitExceededException
     * @throws InvalidRequestException
     */
    protected function parseResponse(string $response = null): AddressCollection
    {
        if (!$response) {
            return null;
        }

        $content = json_decode($response, true);

        $this->checkResponseStatus(
            $content['status'],
            $this->getResponseErrors($content)
        );

        return $this->getResponseResults($content);
    }

    /**
     * @param array $content
     *
     * @return string
     */
    protected function getResponseErrors(array $content): string
    {
        if (array_key_exists('error_message', $content)) {
            return $content['error_message'];
        }

        return '';
    }

    /**
     * @param array $content
     *
     * @return AddressCollection
     */
    protected function getResponseResults(array $content): AddressCollection
    {
        if ($content['status'] === 'ZERO_RESULTS') {
            return new AddressCollection([]);
        }

        return new AddressCollection((array) $content['results']);
    }

    /**
     * @param bool $secure
     *
     * @throws InvalidArgumentException
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

    /**
     * @param string $status
     * @param string $error
     *
     * @throws InvalidRequestException
     * @throws QuotaLimitExceededException
     * @throws RequestDeniedException
     * @throws UnknownErrorException
     */
    protected function checkResponseStatus(string $status, string $error = '')
    {
        switch ($status) {
            case 'OVER_QUERY_LIMIT':
                throw new QuotaLimitExceededException($error);

            case 'REQUEST_DENIED':
                throw new RequestDeniedException($error);

            case 'INVALID_REQUEST':
                throw new InvalidRequestException($error);

            case 'UNKNOWN_ERROR':
                throw new UnknownErrorException($error);
        }
    }
}
