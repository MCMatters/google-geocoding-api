<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeoCoding\Exceptions;

use Exception;

/**
 * Class UnknownErrorException
 *
 * @package McMatters\GoogleGeoCoding\Exceptions
 */
class UnknownErrorException extends Exception implements GeoCodingException
{
    /**
     * UnknownErrorException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
