<?php

namespace App\Jobs;

use App\Services\Integration\SatuSehatService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncSatuSehatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly int $visitId,
        private readonly string $action,
    ) {}

    public function handle(SatuSehatService $service): void
    {
        match ($this->action) {
            'encounter'  => $service->sendEncounter($this->visitId),
            'patient'    => $service->syncPatient($this->visitId),
            'condition'  => $service->sendCondition($this->visitId),
            'medication' => $service->sendMedication($this->visitId),
            default      => Log::warning('SyncSatuSehatJob: unknown action', ['action' => $this->action, 'visit_id' => $this->visitId]),
        };
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function failed(Throwable $exception): void
    {
        Log::error('SyncSatuSehatJob: job failed after all retries', [
            'visit_id' => $this->visitId,
            'action'   => $this->action,
            'error'    => $exception->getMessage(),
        ]);
    }
}
