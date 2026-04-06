<?php

namespace Tests\Unit\Services;

use App\Services\Integration\RetryPolicy;
use Tests\TestCase;

class RetryPolicyTest extends TestCase
{
    private array $sleepCalls = [];

    private function makeSleepRecorder(): callable
    {
        return function (int $seconds): void {
            $this->sleepCalls[] = $seconds;
        };
    }

    public function test_returns_result_on_first_success(): void
    {
        $policy = new RetryPolicy($this->makeSleepRecorder());
        $result = $policy->execute(fn() => 'ok', 'test');
        $this->assertSame('ok', $result);
        $this->assertEmpty($this->sleepCalls);
    }

    public function test_retries_and_succeeds_on_second_attempt(): void
    {
        $attempts = 0;
        $policy   = new RetryPolicy($this->makeSleepRecorder());

        $result = $policy->execute(function () use (&$attempts) {
            $attempts++;
            if ($attempts < 2) throw new \Exception('transient error');
            return 'recovered';
        }, 'test');

        $this->assertSame('recovered', $result);
        $this->assertSame(2, $attempts);
        $this->assertCount(1, $this->sleepCalls);
        $this->assertSame(60, $this->sleepCalls[0]);
    }

    public function test_uses_correct_intervals_between_retries(): void
    {
        $policy = new RetryPolicy($this->makeSleepRecorder());
        try {
            $policy->execute(fn() => throw new \Exception('always fails'), 'test');
        } catch (\Exception) {}

        $this->assertSame([60, 300], $this->sleepCalls);
    }

    public function test_throws_last_exception_after_max_attempts(): void
    {
        $policy = new RetryPolicy($this->makeSleepRecorder());
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('final error');
        $policy->execute(fn() => throw new \Exception('final error'), 'test');
    }

    public function test_retries_exactly_max_attempts_times(): void
    {
        $calls  = 0;
        $policy = new RetryPolicy($this->makeSleepRecorder());
        try {
            $policy->execute(function () use (&$calls) {
                $calls++;
                throw new \Exception('fail');
            }, 'test');
        } catch (\Exception) {}
        $this->assertSame(3, $calls);
    }

    public function test_exposes_correct_intervals(): void
    {
        $policy = new RetryPolicy();
        $this->assertSame([60, 300, 900], $policy->getIntervals());
    }

    public function test_does_not_sleep_after_last_failed_attempt(): void
    {
        $policy = new RetryPolicy($this->makeSleepRecorder());
        try {
            $policy->execute(fn() => throw new \Exception('fail'), 'test');
        } catch (\Exception) {}
        $this->assertCount(2, $this->sleepCalls);
    }
}
