<?php

namespace App\Services\Integration;

use App\Models\ApiSetting;
use App\Models\Visit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Kemenkes integration service for RL (Laporan Rumah Sakit) reporting.
 * Requirements: 16.1–16.4
 */
class KemenkesService implements ExternalApiServiceInterface
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
            $this->config = ApiSetting::where('integration_name', 'kemenkes')->first();
            if ($this->config) {
                $this->testingMode = $this->config->isTestingMode();
                $this->endpoint    = $this->testingMode
                    ? ($this->config->sandbox_url ?? $this->config->endpoint_url)
                    : $this->config->endpoint_url;
            }
        } catch (\Exception $e) {
            Log::error('KemenkesService: Failed to load config', ['error' => $e->getMessage()]);
        }
    }

    public function isTestingMode(): bool { return $this->testingMode; }

    public function send(array $payload): array
    {
        if ($this->isTestingMode()) return $this->mockService->send(array_merge($payload, ['integration' => 'kemenkes']));
        try {
            return Http::withHeaders($this->buildHeaders())->timeout(30)->post($this->endpoint, $payload)->json() ?? [];
        } catch (\Exception $e) {
            Log::error('KemenkesService::send failed', ['error' => $e->getMessage()]);
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
     * Generate RL report data from Visit records for the given period (Req 16.1).
     * @param string $period Format: 'YYYY-MM'
     */
    public function generateRL(string $period): array
    {
        [$year, $month] = explode('-', $period);

        $visits = Visit::with(['patient', 'poli', 'diagnoses'])
            ->whereYear('tanggal_kunjungan', $year)
            ->whereMonth('tanggal_kunjungan', $month)
            ->get();

        return [
            'period'          => $period,
            'generated_at'    => now()->toIso8601String(),
            'total_visits'    => $visits->count(),
            'visits_by_poli'  => $visits->groupBy('poli_id')->map->count()->toArray(),
            'visits_by_payer' => $visits->groupBy('jenis_penjamin')->map->count()->toArray(),
            'top_diagnoses'   => $visits->flatMap->diagnoses->groupBy('icd10_code')->map->count()->sortDesc()->take(10)->toArray(),
        ];
    }

    /**
     * Validate report completeness before sending (Req 16.4).
     */
    public function validateReport(array $reportData): array
    {
        $errors = [];
        if (empty($reportData['period'])) $errors[] = 'Field "period" wajib diisi.';
        if (! isset($reportData['total_visits'])) $errors[] = 'Field "total_visits" wajib diisi.';
        if (empty($reportData['visits_by_poli'])) $errors[] = 'Data kunjungan per poli tidak boleh kosong.';
        if (empty($reportData['visits_by_payer'])) $errors[] = 'Data kunjungan per jenis penjamin tidak boleh kosong.';
        return ['valid' => empty($errors), 'errors' => $errors];
    }

    /**
     * Validate then send report to Kemenkes API (Req 16.2, 16.3, 16.4).
     */
    public function sendReport(array $reportData): array
    {
        $validation = $this->validateReport($reportData);
        if (! $validation['valid']) {
            Log::warning('KemenkesService::sendReport: Validation failed', ['errors' => $validation['errors']]);
            return ['success' => false, 'message' => 'Validasi laporan gagal.', 'errors' => $validation['errors']];
        }

        $result = $this->send($reportData);
        if (empty($result['success']) || $result['success'] === false) {
            Log::error('KemenkesService::sendReport: Pengiriman laporan gagal', ['period' => $reportData['period'] ?? null, 'result' => $result]);
        }
        return $result;
    }

    private function buildHeaders(): array
    {
        return ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
    }
}
