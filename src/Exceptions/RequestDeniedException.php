<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Exceptions;

use Exception;

/**
 * Class RequestDeniedException
 *
 * @package McMatters\GoogleGeocoding\Exceptions
 */
class RequestDeniedException extends Exception implements GeoCodingException
{
    /**
     * RequestDeniedException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
