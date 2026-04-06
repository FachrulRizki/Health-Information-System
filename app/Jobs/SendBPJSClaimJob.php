<?php

namespace App\Jobs;

use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBPJSClaimJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(private readonly int $billId) {}

    public function handle(): void
    {
        $bill = Bill::find($this->billId);
        if (! $bill) {
            Log::warning("SendBPJSClaimJob: Bill #{$this->billId} not found.");
            return;
        }

        // BPJSService::submitClaim() will be integrated in task 11.3
        Log::info("SendBPJSClaimJob: Dispatched for bill #{$this->billId}");
        $bill->update(['bpjs_claim_status' => 'submitted']);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendBPJSClaimJob: Failed for bill #{$this->billId} — {$exception->getMessage()}");
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }
}
