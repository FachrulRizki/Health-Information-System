<?php

namespace App\Jobs;

use App\Services\Integration\KemenkesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendKemenkesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly array $reportData,
        private readonly string $period
    ) {}

    public function handle(KemenkesService $service): void
    {
        Log::info("SendKemenkesReportJob: Mengirim laporan RL periode {$this->period}");
        $result = $service->sendReport($this->reportData);

        if (empty($result['success']) || $result['success'] === false) {
            throw new \RuntimeException('Pengiriman laporan Kemenkes gagal: '.($result['message'] ?? 'Unknown error'));
        }

        Log::info("SendKemenkesReportJob: Laporan RL periode {$this->period} berhasil dikirim");
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendKemenkesReportJob: Gagal mengirim laporan RL periode {$this->period}", ['error' => $exception->getMessage()]);
    }
}
