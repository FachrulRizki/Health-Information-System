<?php

namespace App\Jobs;

use App\Models\AuditTrail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogAuditTrailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly ?int $userId,
        private readonly string $action,
        private readonly ?string $modelType,
        private readonly ?int $modelId,
        private readonly ?array $oldValues,
        private readonly ?array $newValues,
        private readonly ?string $ipAddress,
    ) {}

    public function handle(): void
    {
        AuditTrail::create([
            'user_id'    => $this->userId,
            'action'     => $this->action,
            'model_type' => $this->modelType,
            'model_id'   => $this->modelId,
            'old_values' => $this->oldValues,
            'new_values' => $this->newValues,
            'ip_address' => $this->ipAddress,
        ]);
    }
}
