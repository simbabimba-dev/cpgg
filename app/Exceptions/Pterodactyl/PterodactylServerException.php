<?php

namespace App\Exceptions\Pterodactyl;

/**
 * Thrown when Pterodactyl returns a server-side error (HTTP 5xx).
 */
class PterodactylServerException extends PterodactylException
{
    /**
     * @param  string  $message
     * @param  int  $statusCode
     * @param  \Throwable|null  $previous
     */
    public function __construct(string $message = 'Pterodactyl server error.', int $statusCode = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
    }
}
