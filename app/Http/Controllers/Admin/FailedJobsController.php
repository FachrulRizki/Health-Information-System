<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FailedJobsController extends Controller
{
    /** Threshold for alert banner */
    private const ALERT_THRESHOLD = 5;

    /**
     * List all failed jobs.
     */
    public function index(): View
    {
        $failedJobs = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);
                $job->job_class = $payload['displayName'] ?? ($payload['job'] ?? 'Unknown');
                return $job;
            });

        $threshold = self::ALERT_THRESHOLD;

        return view('admin.failed-jobs', compact('failedJobs', 'threshold'));
    }

    /**
     * Retry a specific failed job by UUID.
     */
    public function retry(string $uuid): RedirectResponse
    {
        $exists = DB::table('failed_jobs')->where('uuid', $uuid)->exists();

        if (! $exists) {
            return redirect()->route('admin.failed-jobs.index')
                ->withErrors(['error' => "Job dengan UUID {$uuid} tidak ditemukan."]);
        }

        Artisan::call('queue:retry', ['id' => [$uuid]]);

        return redirect()->route('admin.failed-jobs.index')
            ->with('success', "Job {$uuid} telah dijadwalkan ulang.");
    }

    /**
     * Retry all failed jobs.
     */
    public function retryAll(): RedirectResponse
    {
        Artisan::call('queue:retry', ['id' => ['all']]);

        return redirect()->route('admin.failed-jobs.index')
            ->with('success', 'Semua failed jobs telah dijadwalkan ulang.');
    }

    /**
     * Delete a specific failed job by UUID.
     */
    public function destroy(string $uuid): RedirectResponse
    {
        DB::table('failed_jobs')->where('uuid', $uuid)->delete();

        return redirect()->route('admin.failed-jobs.index')
            ->with('success', "Job {$uuid} telah dihapus.");
    }

    /**
     * Delete all failed jobs.
     */
    public function clearAll(): RedirectResponse
    {
        DB::table('failed_jobs')->truncate();

        return redirect()->route('admin.failed-jobs.index')
            ->with('success', 'Semua failed jobs telah dihapus.');
    }
}
