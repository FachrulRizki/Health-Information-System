<?php

namespace App\Jobs;

use App\Services\Integration\AplicareService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAplicareJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Retry maksimal 3 kali (Requirements: 15.3) */
    public int $tries = 3;

    public function __construct(
        private readonly int $bedId,
        private readonly string $status,
    ) {}

    public function handle(AplicareService $aplicareService): void
    {
        try {
            $result = $aplicareService->updateBedAvailability($this->bedId, $this->status);

            if (! ($result['success'] ?? false)) {
                Log::warning('SyncAplicareJob: updateBedAvailability returned failure', [
                    'bed_id' => $this->bedId,
                    'status' => $this->status,
                    'result' => $result,
                ]);
                $this->fail(new \RuntimeException($result['message'] ?? 'Aplicare sync failed'));
                return;
            }

            Log::info('SyncAplicareJob: bed availability synced', ['bed_id' => $this->bedId, 'status' => $this->status]);
        } catch (\Exception $e) {
            Log::error('SyncAplicareJob: exception during sync', ['bed_id' => $this->bedId, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /** Interval retry: 5 menit tiap percobaan (Requirements: 7.4) */
    public function backoff(): array
    {
        return [300, 300, 300];
    }
}
