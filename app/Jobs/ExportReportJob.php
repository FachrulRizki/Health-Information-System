<?php

namespace App\Jobs;

use App\Exports\DiseasesExport;
use App\Exports\FinancialExport;
use App\Exports\VisitsExport;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string $reportType  visits|diseases|financial
     * @param array  $filters
     * @param int    $userId
     * @param string $format      excel|pdf
     */
    public function __construct(
        private readonly string $reportType,
        private readonly array  $filters,
        private readonly int    $userId,
        private readonly string $format = 'excel',
    ) {}

    public function handle(ReportService $reportService): void
    {
        Storage::makeDirectory('exports');

        $filename  = $this->buildFilename();
        $filepath  = 'exports/' . $filename;
        $fullPath  = storage_path('app/' . $filepath);

        match ($this->reportType) {
            'visits'    => $this->exportVisits($reportService, $fullPath),
            'diseases'  => $this->exportDiseases($reportService, $fullPath),
            'financial' => $this->exportFinancial($reportService, $fullPath),
        };

        // Notify the requesting user via session-based database notification
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new \App\Notifications\ExportReadyNotification($filename, $filepath));
        }
    }

    private function exportVisits(ReportService $service, string $fullPath): void
    {
        $visits = $service->getVisitReport($this->filters);

        if ($this->format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report.pdf.visits', [
                'visits'  => $visits,
                'filters' => $this->filters,
            ])->setPaper('a4', 'landscape');
            file_put_contents($fullPath, $pdf->output());
        } else {
            Excel::store(new VisitsExport($visits), 'exports/' . basename($fullPath), 'local');
        }
    }

    private function exportDiseases(ReportService $service, string $fullPath): void
    {
        $diseases = $service->getDiseaseReport($this->filters);

        if ($this->format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report.pdf.diseases', [
                'diseases' => $diseases,
                'filters'  => $this->filters,
            ]);
            file_put_contents($fullPath, $pdf->output());
        } else {
            Excel::store(new DiseasesExport($diseases), 'exports/' . basename($fullPath), 'local');
        }
    }

    private function exportFinancial(ReportService $service, string $fullPath): void
    {
        $report = $service->getFinancialReport($this->filters);

        if ($this->format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report.pdf.financial', [
                'report'  => $report,
                'filters' => $this->filters,
            ])->setPaper('a4', 'landscape');
            file_put_contents($fullPath, $pdf->output());
        } else {
            Excel::store(new FinancialExport($report), 'exports/' . basename($fullPath), 'local');
        }
    }

    private function buildFilename(): string
    {
        $ext       = $this->format === 'pdf' ? 'pdf' : 'xlsx';
        $timestamp = now()->format('Ymd_His');

        return match ($this->reportType) {
            'visits'    => "laporan-kunjungan_{$timestamp}.{$ext}",
            'diseases'  => "laporan-penyakit_{$timestamp}.{$ext}",
            'financial' => "laporan-keuangan_{$timestamp}.{$ext}",
            default     => "laporan_{$timestamp}.{$ext}",
        };
    }
}
