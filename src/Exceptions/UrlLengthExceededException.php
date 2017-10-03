<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Exceptions;

use Exception;

/**
 * Class UrlLengthExceededException
 *
 * @package McMatters\GoogleGeocoding\Exceptions
 */
class UrlLengthExceededException extends Exception
{
    /**
     * UrlLengthExceededException constructor.
     */
    public function __construct()
    {
        parent::__construct('The url length was exceeded');
    }
}
