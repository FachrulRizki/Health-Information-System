<?php

namespace App\Services\Integration;

use Illuminate\Support\Facades\Log;

/**
 * Retry policy with exponential backoff for external API calls.
 * Requirements: 13.6, 13.7
 */
class RetryPolicy
{
    private array $intervals = [60, 300, 900]; // 1 min, 5 min, 15 min
    private int $maxAttempts = 3;

    /** @var callable */
    private $sleepFn;

    public function __construct(?callable $sleepFn = null)
    {
        $this->sleepFn = $sleepFn ?? fn(int $seconds) => sleep($seconds);
    }

    /**
     * Execute a callable with retry logic.
     *
     * @throws \Exception When all retries are exhausted
     */
    public function execute(callable $operation, string $context = 'API call'): mixed
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $this->maxAttempts; $attempt++) {
            try {
                return $operation();
            } catch (\Exception $e) {
                $lastException = $e;

                Log::warning("RetryPolicy: {$context} failed on attempt {$attempt}/{$this->maxAttempts}", [
                    'context'   => $context,
                    'attempt'   => $attempt,
                    'exception' => $e->getMessage(),
                ]);

                if ($attempt < $this->maxAttempts) {
                    ($this->sleepFn)($this->intervals[$attempt - 1]);
                }
            }
        }

        Log::error("RetryPolicy: {$context} failed after {$this->maxAttempts} attempts — giving up", [
            'context'   => $context,
            'exception' => $lastException?->getMessage(),
        ]);

        throw $lastException;
    }

    public function getIntervals(): array
    {
        return $this->intervals;
    }
}
