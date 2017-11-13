<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Components;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use McMatters\GoogleGeocoding\Collections\AddressCollection;
use McMatters\GoogleGeocoding\Exceptions\InvalidRequestException;
use McMatters\GoogleGeocoding\Exceptions\QuotaLimitExceededException;
use McMatters\GoogleGeocoding\Exceptions\RequestDeniedException;
use McMatters\GoogleGeocoding\Exceptions\UnknownErrorException;
use RuntimeException;
use const false, null, true;
use function array_key_exists, gettype, json_decode, strpos;

/**
 * Class HttpClient
 *
 * @package McMatters\GoogleGeocoding\Components
 */
class HttpClient
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $key;

    /**
     * HttpClient constructor.
     *
     * @param mixed $key
     *
     * @throws InvalidArgumentException
     */
    public function __construct($key)
    {
        $this->httpClient = new Client();
        $this->setKey($key);
    }

    /**
     * @param string $url
     *
     * @return AddressCollection|null
     * @throws UnknownErrorException
     * @throws RequestDeniedException
     * @throws QuotaLimitExceededException
     * @throws InvalidRequestException
     * @throws RuntimeException
     * @throws ClientException
     */
    public function get(string $url)
    {
        $url = $this->appendKeyToUrl($url);

        try {
            $response = $this->httpClient->get($url)->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if (null === $response) {
                throw $e;
            }

            $response = $response->getBody()->getContents();
        }

        return $this->parseResponse($response);
    }

    /**
     * @param string|null $response
     *
     * @return AddressCollection|null
     * @throws UnknownErrorException
     * @throws RequestDeniedException
     * @throws QuotaLimitExceededException
     * @throws InvalidRequestException
     */
    protected function parseResponse(string $response = null)
    {
        if (!$response) {
            return new AddressCollection([]);
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

    /**
     * @param mixed $key
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function setKey($key)
    {
        switch (gettype($key)) {
            case 'array':
                if (empty($key['client']) || empty($key['signature'])) {
                    throw new InvalidArgumentException(
                        '$apiKey must contain "client" and "signature" values.'
                    );
                }

                $this->key = "client={$key['client']}&signature={$key['signature']}";
                break;

            case 'string':
                $this->key = "key={$key}";
                break;

            default:
                throw new InvalidArgumentException('$key must be string or array');
        }
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function appendKeyToUrl(string $url): string
    {
        return $url.(strpos($url, '?') !== false ? '&' : '?').$this->key;
    }
}
