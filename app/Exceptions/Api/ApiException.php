<?php

namespace App\Exceptions\Api;

use Exception;

/**
 * Base exception for API-facing errors.
 *
 * Carries an HTTP status code so the exception handler can render a
 * meaningful JSON response instead of a generic 500.
 */
class ApiException extends Exception
{
    /**
     * The HTTP status code to return to the client.
     *
     * @var int
     */
    protected int $statusCode;

    /**
     * @param  string  $message
     * @param  int  $statusCode
     * @param  \Throwable|null  $previous
     */
    public function __construct(string $message = '', int $statusCode = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->statusCode = $statusCode;
    }

    /**
     * Get the HTTP status code for this exception.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
