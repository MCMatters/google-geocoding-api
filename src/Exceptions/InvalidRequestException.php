<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding\Exceptions;

use Exception;

/**
 * Class InvalidRequestException
 *
 * @package McMatters\GoogleGeoCoding\Exceptions
 */
class InvalidRequestException extends Exception implements GeoCodingException
{
    /**
     * InvalidRequestException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
