<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding\Components;

use InvalidArgumentException;
use McMatters\GoogleGeoCoding\Collections\AddressCollection;
use McMatters\GoogleGeoCoding\Exceptions\InvalidRequestException;
use McMatters\GoogleGeoCoding\Exceptions\QuotaLimitExceededException;
use McMatters\GoogleGeoCoding\Exceptions\RequestDeniedException;
use McMatters\GoogleGeoCoding\Exceptions\UnknownErrorException;
use McMatters\Ticl\Client;

use function gettype;

/**
 * Class HttpClient
 *
 * @package McMatters\GoogleGeoCoding\Components
 */
class HttpClient
{
    /**
     * @var \McMatters\Ticl\Client
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $key;

    /**
     * HttpClient constructor.
     *
     * @param array|string $key
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($key)
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://maps.googleapis.com/maps/api/geocode/json',
        ]);

        $this->setKey($key);
    }

    /**
     * @param array $query
     *
     * @return \McMatters\GoogleGeoCoding\Collections\AddressCollection|null
     *
     * @throws \McMatters\GoogleGeoCoding\Exceptions\GeoCodingException
     * @throws \Throwable
     */
    public function get(array $query = []): ?AddressCollection
    {
        $data = $this->httpClient
            ->withQuery($this->key + $query)
            ->get('/')
            ->json();

        if (!$data) {
            return new AddressCollection([]);
        }

        $this->checkResponseStatus($data['status'], $data['error_message'] ?? '');

        return $this->getResponseResults($data);
    }

    /**
     * @param array $content
     *
     * @return \McMatters\GoogleGeoCoding\Collections\AddressCollection
     */
    protected function getResponseResults(array $content): AddressCollection
    {
        if ($content['status'] === 'ZERO_RESULTS') {
            return new AddressCollection([]);
        }

        return new AddressCollection((array) $content['results']);
    }

    /**
     * @param string $status
     * @param string $error
     *
     * @return void
     *
     * @throws \McMatters\GoogleGeoCoding\Exceptions\GeoCodingException
     */
    protected function checkResponseStatus(string $status, string $error = ''): void
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

    /**
     * @param array|string $key
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function setKey($key): void
    {
        switch (gettype($key)) {
            case 'array':
                if (empty($key['client']) && empty($key['signature'])) {
                    throw new InvalidArgumentException(
                        '$apiKey must contain "client" and "signature" values.'
                    );
                }

                $this->key = $key;
                break;

            case 'string':
                $this->key = ['key' => $key];
                break;

            default:
                throw new InvalidArgumentException('$key must be string or array');
        }
    }
}
