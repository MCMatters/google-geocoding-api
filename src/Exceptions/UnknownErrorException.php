<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Exceptions;

use Exception;

/**
 * Class UnknownErrorException
 *
 * @package McMatters\GoogleGeocoding\Exceptions
 */
class UnknownErrorException extends Exception
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
