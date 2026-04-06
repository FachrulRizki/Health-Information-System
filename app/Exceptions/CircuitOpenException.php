<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a circuit breaker is open and the request is rejected.
 */
class CircuitOpenException extends RuntimeException
{
    public function __construct(string $service)
    {
        parent::__construct("Circuit breaker terbuka untuk layanan '{$service}'. Silakan coba lagi nanti.");
    }
}
