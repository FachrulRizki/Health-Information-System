<?php

namespace App\Services\Integration;

use App\Exceptions\CircuitOpenException;
use App\Models\ApiSetting;
use App\Services\CircuitBreaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * BPJS VClaim integration service.
 * Requirements: 13.1–13.10
 */
class BPJSService implements ExternalApiServiceInterface
{
    private ?ApiSetting $config = null;
    private ?string $consumerKey = null;
    private ?string $consumerSecret = null;
    private ?string $endpoint = null;
    private bool $testingMode = true;

    private const CB_SERVICE = 'bpjs_vclaim';

    public function __construct(
        private readonly MockApiService $mockService,
        private readonly CircuitBreaker $circuitBreaker,
    ) {
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        try {
            $this->config = ApiSetting::where('integration_name', 'bpjs_vclaim')->first();
            if ($this->config) {
                $this->testingMode    = $this->config->isTestingMode();
                $this->endpoint       = $this->testingMode
                    ? ($this->config->sandbox_url ?? $this->config->endpoint_url)
                    : $this->config->endpoint_url;
                $this->consumerKey    = $this->config->consumer_key;
                $this->consumerSecret = $this->config->consumer_secret;
            }
        } catch (\Exception $e) {
            Log::error('BPJSService: Failed to load config', ['error' => $e->getMessage()]);
        }
    }

    public function isTestingMode(): bool
    {
        return $this->testingMode;
    }

    public function send(array $payload): array
    {
        if ($this->isTestingMode()) {
            return $this->mockService->send($payload);
        }
        if ($this->circuitBreaker->isOpen(self::CB_SERVICE)) {
            throw new CircuitOpenException(self::CB_SERVICE);
        }
        try {
            $result = Http::withHeaders($this->buildHeaders())->timeout(10)->post($this->endpoint, $payload)->json() ?? [];
            $this->circuitBreaker->recordSuccess(self::CB_SERVICE);
            return $result;
        } catch (\Exception $e) {
            Log::error('BPJSService::send failed', ['error' => $e->getMessage()]);
            $this->circuitBreaker->recordFailure(self::CB_SERVICE);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function testConnection(): array
    {
        if ($this->isTestingMode()) {
            return $this->mockService->testConnection();
        }
        if (empty($this->endpoint)) {
            return ['success' => false, 'status_code' => null, 'message' => 'Endpoint URL tidak dikonfigurasi.'];
        }
        try {
            $response = Http::withHeaders($this->buildHeaders())->timeout(10)->get($this->endpoint);
            return [
                'success'     => $response->successful(),
                'status_code' => $response->status(),
                'message'     => $response->successful() ? 'Koneksi berhasil.' : 'Koneksi gagal: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('BPJSService::testConnection failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'status_code' => null, 'message' => 'Koneksi gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Validate BPJS peserta. Caches result for 1 hour (Req 13.2).
     */
    public function validatePeserta(string $noKartu): array
    {
        $cacheKey = "bpjs:peserta:{$noKartu}";

        return Cache::remember($cacheKey, 3600, function () use ($noKartu) {
            if ($this->isTestingMode()) {
                return $this->mockService->send(['noKartu' => $noKartu]);
            }
            try {
                $url      = rtrim($this->endpoint, '/') . "/Peserta/nokartu/{$noKartu}";
                $response = Http::withHeaders($this->buildHeaders())->timeout(10)->get($url);
                return $response->json() ?? ['success' => false, 'message' => 'Empty response'];
            } catch (\Exception $e) {
                Log::error('BPJSService::validatePeserta failed', ['noKartu' => $noKartu, 'error' => $e->getMessage()]);
                return ['success' => false, 'message' => $e->getMessage()];
            }
        });
    }

    /**
     * Insert SEP via BPJS VClaim (Req 13.3).
     */
    public function insertSEP(array $data): array
    {
        if ($this->isTestingMode()) {
            return $this->mockService->send(array_merge($data, ['_action' => 'insertSEP']));
        }
        try {
            $url      = rtrim($this->endpoint, '/') . '/SEP/insert';
            $response = Http::withHeaders($this->buildHeaders())->timeout(10)->post($url, $data);
            return $response->json() ?? ['success' => false, 'message' => 'Empty response'];
        } catch (\Exception $e) {
            Log::error('BPJSService::insertSEP failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update SEP via BPJS VClaim (Req 13.4).
     */
    public function updateSEP(array $data): array
    {
        if ($this->isTestingMode()) {
            return $this->mockService->send(array_merge($data, ['_action' => 'updateSEP']));
        }
        try {
            $url      = rtrim($this->endpoint, '/') . '/SEP/update';
            $response = Http::withHeaders($this->buildHeaders())->timeout(10)->put($url, $data);
            return $response->json() ?? ['success' => false, 'message' => 'Empty response'];
        } catch (\Exception $e) {
            Log::error('BPJSService::updateSEP failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Monitor klaim BPJS (Req 13.5).
     */
    public function monitorKlaim(): array
    {
        if ($this->isTestingMode()) {
            return $this->mockService->send(['_action' => 'monitorKlaim']);
        }
        try {
            $url      = rtrim($this->endpoint, '/') . '/monitoring/klaim';
            $response = Http::withHeaders($this->buildHeaders())->timeout(10)->get($url);
            return $response->json() ?? ['success' => false, 'message' => 'Empty response'];
        } catch (\Exception $e) {
            Log::error('BPJSService::monitorKlaim failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function buildHeaders(): array
    {
        $timestamp = now()->format('YmdHis');
        return [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
            'X-cons-id'    => $this->consumerKey ?? '',
            'X-timestamp'  => $timestamp,
            'X-signature'  => $this->buildSignature($timestamp),
        ];
    }

    private function buildSignature(string $timestamp): string
    {
        if (empty($this->consumerKey) || empty($this->consumerSecret)) {
            return '';
        }
        return base64_encode(hash_hmac('sha256', $this->consumerKey . '&' . $timestamp, $this->consumerSecret, true));
    }
}
