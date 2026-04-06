<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Visit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ClaimsController extends Controller
{
    /**
     * Halaman pencarian pasien untuk berkas digital.
     */
    public function search(Request $request): View
    {
        $q        = $request->query('q', '');
        $patients = collect();

        if ($q !== '') {
            $patients = Patient::where('nama_lengkap', 'like', "%{$q}%")
                ->orWhere('no_rm', 'like', "%{$q}%")
                ->orWhere('no_bpjs', 'like', "%{$q}%")
                ->orderBy('nama_lengkap')
                ->paginate(15)
                ->withQueryString();
        }

        return view('claims.search', compact('q', 'patients'));
    }

    /**
     * Repositori berkas digital per pasien — dengan sub-menu rawat jalan & rawat inap.
     */
    public function index(int $patientId, Request $request): View
    {
        $patient   = Patient::findOrFail($patientId);
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $tab       = $request->query('tab', 'rawat_jalan');

        $baseQuery = Visit::with(['poli', 'doctor', 'diagnoses.icd10Code', 'bill', 'medicalRecord', 'inpatientRecord.bed.room'])
            ->where('patient_id', $patientId)
            ->when($startDate, fn($q) => $q->whereDate('tanggal_kunjungan', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tanggal_kunjungan', '<=', $endDate))
            ->orderByDesc('tanggal_kunjungan');

        // Rawat jalan: tidak punya inpatientRecord (belum/tidak masuk rawat inap)
        $rawatJalan = (clone $baseQuery)->whereDoesntHave('inpatientRecord')->get();

        // Rawat inap: punya inpatientRecord
        $rawatInap = (clone $baseQuery)->whereHas('inpatientRecord')->get();

        return view('claims.index', compact('patient', 'rawatJalan', 'rawatInap', 'tab', 'startDate', 'endDate'));
    }

    /**
     * Detail klaim satu kunjungan (Req 12.1, 12.2).
     */
    public function show(int $visitId): View
    {
        $visit = Visit::with(['patient', 'poli', 'doctor', 'diagnoses.icd10Code', 'procedures.icd9cmCode', 'bill.items', 'medicalRecord'])
            ->findOrFail($visitId);

        return view('claims.show', compact('visit'));
    }

    /**
     * Buat draft klaim BPJS (Req 12.2).
     */
    public function createDraft(int $visitId): JsonResponse
    {
        $visit = Visit::with(['patient', 'poli', 'doctor', 'diagnoses.icd10Code', 'procedures.icd9cmCode', 'bill.items'])
            ->findOrFail($visitId);

        $primaryDiagnosis = $visit->diagnoses->firstWhere('is_primary', true) ?? $visit->diagnoses->first();

        $draft = [
            'no_rawat'          => $visit->no_rawat,
            'no_sep'            => $visit->no_sep,
            'tanggal_kunjungan' => $visit->tanggal_kunjungan?->format('Y-m-d'),
            'jenis_penjamin'    => $visit->jenis_penjamin,
            'pasien'            => [
                'no_rm'         => $visit->patient?->no_rm,
                'nama_lengkap'  => $visit->patient?->nama_lengkap,
                'no_bpjs'       => $visit->patient?->no_bpjs,
                'tanggal_lahir' => $visit->patient?->tanggal_lahir?->format('Y-m-d'),
                'jenis_kelamin' => $visit->patient?->jenis_kelamin,
            ],
            'poli'           => $visit->poli?->nama_poli,
            'dokter'         => $visit->doctor?->nama_dokter,
            'diagnosa_utama' => $primaryDiagnosis ? ['kode' => $primaryDiagnosis->icd10_code, 'deskripsi' => $primaryDiagnosis->icd10Code?->deskripsi] : null,
            'diagnosa_lain'  => $visit->diagnoses->where('is_primary', false)->map(fn($d) => ['kode' => $d->icd10_code, 'deskripsi' => $d->icd10Code?->deskripsi])->values(),
            'tindakan'       => $visit->procedures->map(fn($p) => ['kode' => $p->icd9cm_code, 'deskripsi' => $p->icd9cmCode?->deskripsi])->values(),
            'tagihan'        => ['total' => $visit->bill?->total_amount, 'status' => $visit->bill?->status, 'bpjs_claim_status' => $visit->bill?->bpjs_claim_status],
            'generated_at'   => now()->toIso8601String(),
        ];

        return response()->json(['success' => true, 'data' => $draft]);
    }

    /**
     * Ekspor dokumen klaim sebagai PDF (Req 12.3, 12.4).
     */
    public function exportPdf(int $visitId): Response
    {
        $visit = Visit::with(['patient', 'poli', 'doctor', 'diagnoses.icd10Code', 'procedures.icd9cmCode', 'bill.items', 'medicalRecord'])
            ->findOrFail($visitId);

        $primaryDiagnosis = $visit->diagnoses->firstWhere('is_primary', true) ?? $visit->diagnoses->first();

        $pdf      = Pdf::loadView('claims.pdf', compact('visit', 'primaryDiagnosis'))->setPaper('a4', 'portrait');
        $filename = 'klaim-'.$visit->no_rawat.'-'.now()->format('Ymd').'.pdf';

        return $pdf->download($filename);
    }
}
