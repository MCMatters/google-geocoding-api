<?php

declare(strict_types = 1);

namespace McMatters\GoogleGeocoding\Exceptions;

use Exception;

/**
 * Class QuotaLimitExceededException
 *
 * @package McMatters\GoogleGeocoding\Exceptions
 */
class QuotaLimitExceededException extends Exception
{
    /**
     * QuotaLimitExceededException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = 'You are over your quota')
    {
        parent::__construct($message);
    }
}
