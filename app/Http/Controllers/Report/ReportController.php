<?php

namespace App\Http\Controllers\Report;

use App\Exports\DiseasesExport;
use App\Exports\FinancialExport;
use App\Exports\VisitsExport;
use App\Http\Controllers\Controller;
use App\Jobs\ExportReportJob;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    /**
     * Laporan kunjungan pasien (Req 17.1)
     */
    public function visits(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'poli_id', 'doctor_id', 'jenis_penjamin']);
        $visits  = $request->hasAny(['date_from', 'date_to', 'poli_id', 'doctor_id', 'jenis_penjamin'])
            ? $this->reportService->getVisitReport($filters)
            : collect();

        return view('report.visits', [
            'visits'  => $visits,
            'filters' => $filters,
            'polis'   => $this->reportService->getPolis(),
            'doctors' => $this->reportService->getDoctors(),
        ]);
    }

    /**
     * Laporan penyakit berdasarkan ICD-10 (Req 17.2)
     */
    public function diseases(Request $request)
    {
        $filters  = $request->only(['date_from', 'date_to']);
        $diseases = $request->hasAny(['date_from', 'date_to'])
            ? $this->reportService->getDiseaseReport($filters)
            : collect();

        return view('report.diseases', [
            'diseases' => $diseases,
            'filters'  => $filters,
        ]);
    }

    /**
     * Laporan keuangan (Req 17.3)
     */
    public function financial(Request $request)
    {
        $filters  = $request->only(['date_from', 'date_to']);
        $report   = $request->hasAny(['date_from', 'date_to'])
            ? $this->reportService->getFinancialReport($filters)
            : null;

        return view('report.financial', [
            'report'  => $report,
            'filters' => $filters,
        ]);
    }

    // ── Export: Visits ────────────────────────────────────────────────────────

    public function exportVisitsPdf(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'poli_id', 'doctor_id', 'jenis_penjamin']);
        $count   = $this->reportService->getVisitReport($filters)->count();

        if ($count > 1000) {
            ExportReportJob::dispatch('visits', $filters, (int) auth()->id(), 'pdf');
            return $this->asyncResponse($request);
        }

        $visits = $this->reportService->getVisitReport($filters);
        $pdf    = Pdf::loadView('report.pdf.visits', compact('visits', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kunjungan.pdf');
    }

    public function exportVisitsExcel(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'poli_id', 'doctor_id', 'jenis_penjamin']);
        $count   = $this->reportService->getVisitReport($filters)->count();

        if ($count > 1000) {
            ExportReportJob::dispatch('visits', $filters, (int) auth()->id(), 'excel');
            return $this->asyncResponse($request);
        }

        $visits = $this->reportService->getVisitReport($filters);

        return Excel::download(new VisitsExport($visits), 'laporan-kunjungan.xlsx');
    }

    // ── Export: Diseases ─────────────────────────────────────────────────────

    public function exportDiseasesPdf(Request $request)
    {
        $filters  = $request->only(['date_from', 'date_to']);
        $count    = $this->reportService->getDiseaseReport($filters)->count();

        if ($count > 1000) {
            ExportReportJob::dispatch('diseases', $filters, (int) auth()->id(), 'pdf');
            return $this->asyncResponse($request);
        }

        $diseases = $this->reportService->getDiseaseReport($filters);
        $pdf      = Pdf::loadView('report.pdf.diseases', compact('diseases', 'filters'));

        return $pdf->download('laporan-penyakit.pdf');
    }

    public function exportDiseasesExcel(Request $request)
    {
        $filters  = $request->only(['date_from', 'date_to']);
        $count    = $this->reportService->getDiseaseReport($filters)->count();

        if ($count > 1000) {
            ExportReportJob::dispatch('diseases', $filters, (int) auth()->id(), 'excel');
            return $this->asyncResponse($request);
        }

        $diseases = $this->reportService->getDiseaseReport($filters);

        return Excel::download(new DiseasesExport($diseases), 'laporan-penyakit.xlsx');
    }

    // ── Export: Financial ────────────────────────────────────────────────────

    public function exportFinancialPdf(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to']);
        $report  = $this->reportService->getFinancialReport($filters);
        $count   = $report['bills']->count();

        if ($count > 1000) {
            ExportReportJob::dispatch('financial', $filters, (int) auth()->id(), 'pdf');
            return $this->asyncResponse($request);
        }

        $pdf = Pdf::loadView('report.pdf.financial', compact('report', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-keuangan.pdf');
    }

    public function exportFinancialExcel(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to']);
        $report  = $this->reportService->getFinancialReport($filters);
        $count   = $report['bills']->count();

        if ($count > 1000) {
            ExportReportJob::dispatch('financial', $filters, (int) auth()->id(), 'excel');
            return $this->asyncResponse($request);
        }

        return Excel::download(new FinancialExport($report), 'laporan-keuangan.xlsx');
    }

    // ── Download async export ─────────────────────────────────────────────────

    /**
     * Download a previously generated async export file (Req 17.5)
     */
    public function downloadExport(Request $request, string $filename)
    {
        // Sanitize: only allow safe filenames (no path traversal)
        $filename = basename($filename);
        $filepath = 'exports/' . $filename;

        if (!Storage::exists($filepath)) {
            abort(404, 'File ekspor tidak ditemukan.');
        }

        return Storage::download($filepath, $filename);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Return an immediate "processing" response for async exports.
     */
    private function asyncResponse(Request $request)
    {
        $message = 'Data terlalu besar (>1000 baris). Ekspor sedang diproses di latar belakang. Anda akan mendapat notifikasi ketika file siap diunduh.';

        if ($request->expectsJson()) {
            return response()->json(['status' => 'processing', 'message' => $message]);
        }

        return back()->with('info', $message);
    }
}
