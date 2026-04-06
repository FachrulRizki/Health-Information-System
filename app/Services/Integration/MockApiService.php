<?php

namespace App\Services\Integration;

/**
 * Mock API service for testing mode.
 * Returns realistic mock responses for all external integrations.
 * Used when any integration is configured in testing mode.
 */
class MockApiService implements ExternalApiServiceInterface
{
    public function __construct(
        private readonly string $integration = 'default'
    ) {}

    public function send(array $payload): array
    {
        return match ($this->integration) {
            'bpjs', 'bpjs_vclaim' => $this->mockBpjsResponse($payload),
            'satusehat' => $this->mockSatuSehatResponse($payload),
            'aplicare' => $this->mockAplicareResponse($payload),
            'kemenkes' => $this->mockKemenkesResponse($payload),
            default => ['success' => true, 'message' => 'Mock response', 'data' => []],
        };
    }

    public function testConnection(): array
    {
        return [
            'success' => true,
            'status_code' => 200,
            'message' => 'Mock connection test successful',
            'integration' => $this->integration,
        ];
    }

    public function isTestingMode(): bool
    {
        return true;
    }

    private function mockBpjsResponse(array $payload): array
    {
        return [
            'metaData' => ['code' => '200', 'message' => 'OK'],
            'response' => [
                'peserta' => [
                    'noKartu' => $payload['noKartu'] ?? '0000000000000',
                    'nama' => 'PASIEN MOCK BPJS',
                    'statusPeserta' => ['kode' => '1', 'keterangan' => 'AKTIF'],
                ],
            ],
        ];
    }

    private function mockSatuSehatResponse(array $payload): array
    {
        return [
            'resourceType' => $payload['resourceType'] ?? 'Bundle',
            'id' => 'mock-'.uniqid(),
            'status' => 'active',
        ];
    }

    private function mockAplicareResponse(array $payload): array
    {
        return [
            'status' => 'success',
            'message' => 'Bed availability updated (mock)',
            'data' => $payload,
        ];
    }

    private function mockKemenkesResponse(array $payload): array
    {
        return [
            'status' => 'success',
            'message' => 'Report received (mock)',
            'reference_number' => 'MOCK-'.date('YmdHis'),
        ];
    }
}
