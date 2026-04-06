<?php

namespace App\Services\Integration;

use App\Models\ApiSetting;
use App\Models\Bed;
use App\Models\Room;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Aplicare integration service for bed/room synchronisation.
 * Requirements: 15.1–15.5
 */
class AplicareService implements ExternalApiServiceInterface
{
    private ?ApiSetting $config = null;
    private ?string $endpoint   = null;
    private bool $testingMode   = true;

    public function __construct(private readonly MockApiService $mockService)
    {
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        try {
            $this->config = ApiSetting::where('integration_name', 'aplicare')->first();
            if ($this->config) {
                $this->testingMode = $this->config->isTestingMode();
                $this->endpoint    = $this->testingMode
                    ? ($this->config->sandbox_url ?? $this->config->endpoint_url)
                    : $this->config->endpoint_url;
            }
        } catch (\Exception $e) {
            Log::error('AplicareService: Failed to load config', ['error' => $e->getMessage()]);
        }
    }

    public function isTestingMode(): bool { return $this->testingMode; }

    public function send(array $payload): array
    {
        if ($this->isTestingMode()) return $this->mockService->send($payload);
        try {
            return Http::withHeaders($this->buildHeaders())->timeout(30)->post($this->endpoint, $payload)->json() ?? [];
        } catch (\Exception $e) {
            Log::error('AplicareService::send failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function testConnection(): array
    {
        if ($this->isTestingMode()) return $this->mockService->testConnection();
        if (empty($this->endpoint)) return ['success' => false, 'status_code' => null, 'message' => 'Endpoint tidak dikonfigurasi.'];
        try {
            $response = Http::withHeaders($this->buildHeaders())->timeout(10)->get($this->endpoint);
            return ['success' => $response->successful(), 'status_code' => $response->status(), 'message' => $response->successful() ? 'Koneksi berhasil.' : 'Gagal: '.$response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'status_code' => null, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send bed availability update to Aplicare (Req 15.1).
     */
    public function updateBedAvailability(int $bedId, string $status): array
    {
        $payload = ['bed_id' => $bedId, 'status' => $status];
        if ($this->isTestingMode()) return $this->mockService->send(array_merge($payload, ['integration' => 'aplicare']));
        try {
            $url      = rtrim($this->endpoint, '/').'/beds/'.$bedId.'/availability';
            $response = Http::withHeaders($this->buildHeaders())->timeout(30)->post($url, $payload);
            return $response->json() ?? ['success' => $response->successful()];
        } catch (\Exception $e) {
            Log::error('AplicareService::updateBedAvailability failed', ['bed_id' => $bedId, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Sync rooms and beds from Aplicare into local RME database (Req 15.2).
     */
    public function syncRoomsAndBeds(): array
    {
        if ($this->isTestingMode()) return $this->mockService->send(['action' => 'syncRoomsAndBeds', 'integration' => 'aplicare']);
        try {
            $response = Http::withHeaders($this->buildHeaders())->timeout(30)->get(rtrim($this->endpoint, '/').'/rooms');
            if (! $response->successful()) {
                return ['success' => false, 'message' => 'HTTP '.$response->status()];
            }
            $synced = 0;
            foreach ($response->json('data', []) as $roomData) {
                $room = Room::updateOrCreate(
                    ['kode_kamar' => $roomData['kode_kamar'] ?? $roomData['code']],
                    ['nama_kamar' => $roomData['nama_kamar'] ?? $roomData['name'] ?? '', 'kelas' => $roomData['kelas'] ?? '', 'kapasitas' => $roomData['kapasitas'] ?? 0, 'is_active' => $roomData['is_active'] ?? true]
                );
                foreach ($roomData['beds'] ?? [] as $bedData) {
                    Bed::updateOrCreate(['kode_bed' => $bedData['kode_bed'] ?? $bedData['code'], 'room_id' => $room->id], ['status' => $bedData['status'] ?? 'tersedia']);
                    $synced++;
                }
            }
            return ['success' => true, 'synced_beds' => $synced];
        } catch (\Exception $e) {
            Log::error('AplicareService::syncRoomsAndBeds failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function buildHeaders(): array
    {
        return ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
    }
}
