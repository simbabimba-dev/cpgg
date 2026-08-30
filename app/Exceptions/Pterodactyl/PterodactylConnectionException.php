<?php

namespace App\Exceptions\Pterodactyl;

/**
 * Thrown when the panel cannot connect to a Pterodactyl node or the
 * Pterodactyl API is unreachable.
 */
class PterodactylConnectionException extends PterodactylException
{
    /**
     * @param  string  $message
     * @param  \Throwable|null  $previous
     */
    public function __construct(string $message = 'Unable to connect to Pterodactyl node. Please check if the node is online and accessible.', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
