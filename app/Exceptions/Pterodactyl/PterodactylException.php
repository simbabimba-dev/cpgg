<?php

namespace App\Exceptions\Pterodactyl;

use Exception;

/**
 * Base exception for Pterodactyl API failures.
 *
 * Carries the HTTP status returned by Pterodactyl (when available) and a
 * human-readable hint so callers and the exception handler can respond
 * appropriately.
 */
class PterodactylException extends Exception
{
    /**
     * The HTTP status code returned by Pterodactyl, if any.
     *
     * @var int|null
     */
    protected ?int $statusCode;

    /**
     * @param  string  $message
     * @param  int|null  $statusCode
     * @param  \Throwable|null  $previous
     */
    public function __construct(string $message = '', ?int $statusCode = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->statusCode = $statusCode;
    }

    /**
     * Get the HTTP status code returned by Pterodactyl, if any.
     *
     * @return int|null
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
}
