<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Simple circuit breaker using Redis Cache.
 *
 * States:
 *  - closed   : normal operation
 *  - open     : circuit tripped, requests fail immediately
 *  - half-open: one test request allowed after cooldown
 *
 * Requirements: 13.6
 */
class CircuitBreaker
{
    /** Number of failures before the circuit opens. */
    private const FAILURE_THRESHOLD = 10;

    /** Window (seconds) in which failures are counted. */
    private const FAILURE_WINDOW = 300; // 5 minutes

    /** How long (seconds) the circuit stays open before moving to half-open. */
    private const OPEN_DURATION = 1800; // 30 minutes

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Returns true when the circuit is open (requests should be rejected).
     * Returns false when the circuit is closed or half-open (request may proceed).
     */
    public function isOpen(string $service): bool
    {
        $openUntil = Cache::get($this->openKey($service));

        if ($openUntil === null) {
            // Circuit is closed.
            return false;
        }

        if (now()->timestamp < $openUntil) {
            // Circuit is still open.
            return true;
        }

        // Cooldown elapsed → transition to half-open: allow one request through.
        // We delete the open key so the next isOpen() call returns false.
        Cache::forget($this->openKey($service));
        return false;
    }

    /**
     * Record a failed API call.
     * Opens the circuit when the failure threshold is reached within the window.
     */
    public function recordFailure(string $service): void
    {
        $key   = $this->failureKey($service);
        $count = (int) Cache::get($key, 0);

        if ($count === 0) {
            // Start a new window.
            Cache::put($key, 1, self::FAILURE_WINDOW);
        } else {
            Cache::increment($key);
            $count++;
        }

        if ($count >= self::FAILURE_THRESHOLD) {
            // Open the circuit.
            Cache::put($this->openKey($service), now()->addSeconds(self::OPEN_DURATION)->timestamp, self::OPEN_DURATION);
            Cache::forget($key); // Reset failure counter.
        }
    }

    /**
     * Record a successful API call.
     * Resets the failure counter and closes the circuit.
     */
    public function recordSuccess(string $service): void
    {
        Cache::forget($this->failureKey($service));
        Cache::forget($this->openKey($service));
    }

    // -------------------------------------------------------------------------
    // Cache key helpers
    // -------------------------------------------------------------------------

    private function failureKey(string $service): string
    {
        return "circuit_breaker:{$service}:failures";
    }

    private function openKey(string $service): string
    {
        return "circuit_breaker:{$service}:open_until";
    }
}
