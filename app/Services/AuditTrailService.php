<?php

namespace App\Services;

use App\Jobs\LogAuditTrailJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditTrailService
{
    /**
     * Log an audit trail entry asynchronously.
     */
    public function log(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        LogAuditTrailJob::dispatch(
            Auth::id(),
            $action,
            $modelType,
            $modelId,
            $oldValues,
            $newValues,
            Request::ip(),
        );
    }
}
