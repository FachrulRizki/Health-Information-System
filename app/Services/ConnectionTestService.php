<?php

namespace App\Services;

use App\Models\ApiSetting;
use App\Services\Integration\AplicareService;
use App\Services\Integration\BPJSService;
use App\Services\Integration\ExternalApiServiceInterface;
use App\Services\Integration\MockApiService;
use App\Services\Integration\SatuSehatService;

/**
 * Service untuk menguji koneksi ke API eksternal.
 * Requirements: 21.4, 21.5, 21.6
 */
class ConnectionTestService
{
    public function testConnection(string $integrationName): array
    {
        $setting = ApiSetting::where('integration_name', $integrationName)->first();

        if (! $setting) {
            return [
                'success'     => false,
                'status_code' => null,
                'message'     => "Konfigurasi untuk integrasi '{$integrationName}' tidak ditemukan.",
                'suggestion'  => 'Pastikan integrasi sudah dikonfigurasi melalui menu Setting API.',
            ];
        }

        try {
            $result = $this->resolveService($integrationName)->testConnection();

            return [
                'success'     => $result['success'] ?? false,
                'status_code' => $result['status_code'] ?? null,
                'message'     => $result['message'] ?? 'Tidak ada pesan dari server.',
                'suggestion'  => ($result['success'] ?? false) ? null : $this->getSuggestion($result),
            ];
        } catch (\Exception $e) {
            return [
                'success'     => false,
                'status_code' => null,
                'message'     => 'Uji koneksi gagal: ' . $e->getMessage(),
                'suggestion'  => 'Periksa konfigurasi endpoint URL dan kredensial API, lalu coba lagi.',
            ];
        }
    }

    private function resolveService(string $integrationName): ExternalApiServiceInterface
    {
        return match ($integrationName) {
            'bpjs_vclaim' => app(BPJSService::class),
            'satusehat'   => app(SatuSehatService::class),
            'aplicare'    => app(AplicareService::class),
            default       => new MockApiService($integrationName),
        };
    }

    private function getSuggestion(array $result): string
    {
        $statusCode = $result['status_code'] ?? null;

        if ($statusCode === null) {
            return 'Periksa apakah endpoint URL dapat dijangkau dan tidak ada masalah jaringan.';
        }
        if ($statusCode === 401 || $statusCode === 403) {
            return 'Periksa consumer key dan consumer secret, pastikan kredensial sudah benar dan aktif.';
        }
        if ($statusCode === 404) {
            return 'Endpoint URL tidak ditemukan. Periksa kembali URL yang dikonfigurasi.';
        }
        if ($statusCode >= 500) {
            return 'Server API sedang mengalami gangguan. Coba lagi beberapa saat kemudian.';
        }
        return 'Periksa konfigurasi API dan pastikan server tujuan dapat dijangkau.';
    }
}
