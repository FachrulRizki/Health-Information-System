<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\Poli;
use App\Models\Visit;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Laporan kunjungan pasien (Req 17.1)
     * Filter: date_from, date_to, poli_id, doctor_id, jenis_penjamin
     */
    public function getVisitReport(array $filters): Collection
    {
        $query = Visit::with(['patient', 'poli', 'doctor'])
            ->when(!empty($filters['date_from']), fn($q) => $q->whereDate('tanggal_kunjungan', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']),   fn($q) => $q->whereDate('tanggal_kunjungan', '<=', $filters['date_to']))
            ->when(!empty($filters['poli_id']),    fn($q) => $q->where('poli_id', $filters['poli_id']))
            ->when(!empty($filters['doctor_id']),  fn($q) => $q->where('doctor_id', $filters['doctor_id']))
            ->when(!empty($filters['jenis_penjamin']), fn($q) => $q->where('jenis_penjamin', $filters['jenis_penjamin']))
            ->orderBy('tanggal_kunjungan', 'desc');

        return $query->get();
    }

    /**
     * Laporan penyakit berdasarkan ICD-10 (Req 17.2)
     * Filter: date_from, date_to
     */
    public function getDiseaseReport(array $filters): Collection
    {
        return Diagnosis::with('icd10Code')
            ->when(!empty($filters['date_from']), fn($q) => $q->whereHas('visit', fn($v) => $v->whereDate('tanggal_kunjungan', '>=', $filters['date_from'])))
            ->when(!empty($filters['date_to']),   fn($q) => $q->whereHas('visit', fn($v) => $v->whereDate('tanggal_kunjungan', '<=', $filters['date_to'])))
            ->select('icd10_code')
            ->selectRaw('COUNT(*) as total_kasus')
            ->groupBy('icd10_code')
            ->orderByDesc('total_kasus')
            ->get();
    }

    /**
     * Laporan keuangan: pendapatan tunai dan klaim BPJS (Req 17.3)
     * Filter: date_from, date_to
     */
    public function getFinancialReport(array $filters): array
    {
        $query = Bill::with('visit')
            ->whereHas('visit', function ($q) use ($filters) {
                $q->when(!empty($filters['date_from']), fn($v) => $v->whereDate('tanggal_kunjungan', '>=', $filters['date_from']))
                  ->when(!empty($filters['date_to']),   fn($v) => $v->whereDate('tanggal_kunjungan', '<=', $filters['date_to']));
            })
            ->where('status', 'paid');

        $bills = $query->get();

        $tunai = $bills->whereIn('payment_method', ['umum', 'asuransi'])->sum('total_amount');
        $bpjs  = $bills->where('payment_method', 'bpjs')->sum('total_amount');

        $byMethod = $bills->groupBy('payment_method')->map(fn($group) => [
            'count'  => $group->count(),
            'total'  => $group->sum('total_amount'),
        ]);

        return [
            'total_tunai'  => $tunai,
            'total_bpjs'   => $bpjs,
            'grand_total'  => $tunai + $bpjs,
            'by_method'    => $byMethod,
            'bills'        => $bills,
        ];
    }

    public function getPolis(): Collection
    {
        return Poli::where('is_active', true)->orderBy('nama_poli')->get();
    }

    public function getDoctors(): Collection
    {
        return Doctor::where('is_active', true)->orderBy('nama_dokter')->get();
    }
}
