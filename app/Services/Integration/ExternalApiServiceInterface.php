<?php

namespace App\Services\Integration;

/**
 * Interface for all external API integration services.
 * Implementations: BPJSService, SatuSehatService, AplicareService, KemenkesService, MockApiService
 */
interface ExternalApiServiceInterface
{
    /**
     * Send a payload to the external API.
     */
    public function send(array $payload): array;

    /**
     * Test the connection to the external API.
     * Must respond within 10 seconds per requirement 21.5.
     */
    public function testConnection(): array;

    /**
     * Check if the service is in testing mode.
     * When true, requests must NOT be sent to production API (requirement 13.10, 14.9, 15.5, 21.9).
     */
    public function isTestingMode(): bool;
}
