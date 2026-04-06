<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Bill;
use App\Models\Diagnosis;
use App\Models\DrugStock;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Prescription;
use App\Models\QueueEntry;
use App\Models\RadiologyRequest;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $data = match ($user->role) {
            'admin'                => $this->adminData(),
            'dokter'               => $this->dokterData($user),
            'perawat'              => $this->perawatData(),
            'farmasi'              => $this->farmasiData(),
            'kasir'                => $this->kasirData(),
            'petugas_pendaftaran'  => $this->petugasPendaftaranData(),
            'manajemen'            => $this->manajemenData(),
            default                => [],
        };

        return view('dashboard', array_merge(['role' => $user->role], $data));
    }

    private function commonTodayStats(): array
    {
        $today = Carbon::today();
        $visitsToday = Visit::whereDate('tanggal_kunjungan', $today);
        $total = (clone $visitsToday)->count();
        $selesai = (clone $visitsToday)->where('status', 'selesai')->count();
        $belumSelesai = $total - $selesai;
        $persenSelesai = $total > 0 ? round(($selesai / $total) * 100) : 0;

        // RME completion: visits that have a medicalRecord
        $rmeCount = (clone $visitsToday)->whereHas('medicalRecord')->count();
        $persenRme = $total > 0 ? round(($rmeCount / $total) * 100) : 0;

        // Resep completion: visits that have a dispensed prescription
        $resepCount = (clone $visitsToday)->whereHas('prescriptions', fn($q) => $q->where('status', 'dispensed'))->count();
        $persenResep = $total > 0 ? round(($resepCount / $total) * 100) : 0;

        // Resume: visits that have medicalRecord with assessment filled (proxy for resume)
        $resumeCount = (clone $visitsToday)->whereHas('medicalRecord', fn($q) => $q->whereNotNull('assessment'))->count();
        $persenResume = $total > 0 ? round(($resumeCount / $total) * 100) : 0;

        // Top 10 diseases today
        $top10Penyakit = Diagnosis::select('icd10_code', DB::raw('count(*) as total'))
            ->whereHas('visit', fn($q) => $q->whereDate('tanggal_kunjungan', $today))
            ->groupBy('icd10_code')
            ->orderByDesc('total')
            ->limit(10)
            ->with('icd10Code')
            ->get();

        // Poli aktif dengan jumlah pasien hari ini
        $poliStats = Poli::where('is_active', true)
            ->withCount(['visits' => fn($q) => $q->whereDate('tanggal_kunjungan', $today)])
            ->orderByDesc('visits_count')
            ->get();

        // Progress per poli: SOAP, Resep, Resume
        $poliProgress = Poli::where('is_active', true)
            ->withCount([
                'visits as total_visits' => fn($q) => $q->whereDate('tanggal_kunjungan', $today),
                'visits as soap_done'    => fn($q) => $q->whereDate('tanggal_kunjungan', $today)->whereHas('medicalRecord'),
                'visits as resep_done'   => fn($q) => $q->whereDate('tanggal_kunjungan', $today)->whereHas('prescriptions', fn($p) => $p->where('status', 'dispensed')),
                'visits as resume_done'  => fn($q) => $q->whereDate('tanggal_kunjungan', $today)->whereHas('medicalRecord', fn($m) => $m->whereNotNull('assessment')->where('assessment', '!=', '')),
            ])
            ->get()
            ->map(function ($poli) {
                $total = $poli->total_visits;
                return [
                    'nama'        => $poli->nama_poli,
                    'total'       => $total,
                    'soap_pct'    => $total > 0 ? round($poli->soap_done / $total * 100) : 0,
                    'resep_pct'   => $total > 0 ? round($poli->resep_done / $total * 100) : 0,
                    'resume_pct'  => $total > 0 ? round($poli->resume_done / $total * 100) : 0,
                    'soap_done'   => $poli->soap_done,
                    'resep_done'  => $poli->resep_done,
                    'resume_done' => $poli->resume_done,
                ];
            })
            ->filter(fn($p) => $p['total'] > 0);

        return compact(
            'total', 'selesai', 'belumSelesai', 'persenSelesai',
            'persenRme', 'persenResep', 'persenResume',
            'top10Penyakit', 'poliStats', 'poliProgress'
        );
    }

    private function adminData(): array
    {
        $today  = Carbon::today();
        $common = $this->commonTodayStats();

        $totalToday       = $common['total'];
        $totalSelesai     = $common['selesai'];
        $totalBelumSelesai = $common['belumSelesai'];
        $rmePercent       = $common['persenRme'];
        $resepPercent     = $common['persenResep'];
        $resumePercent    = $common['persenResume'];

        $highAlertDrugs = \App\Models\DrugStock::with('drug')
            ->where(function ($q) {
                $q->whereColumn('quantity', '<=', 'minimum_stock')
                  ->orWhere('expiry_date', '<', Carbon::today())
                  ->orWhere(function ($q2) {
                      $q2->where('expiry_date', '>', Carbon::today())
                         ->where('expiry_date', '<=', Carbon::today()->addDays(30));
                  });
            })
            ->get()
            ->map(function ($stock) {
                $type = 'low';
                if ($stock->expiry_date && $stock->expiry_date->lt(Carbon::today())) {
                    $type = 'expired';
                } elseif ($stock->expiry_date && $stock->expiry_date->lte(Carbon::today()->addDays(30))) {
                    $type = 'near_expiry';
                }
                return [
                    'nama' => $stock->drug?->nama ?? '-',
                    'qty'  => $stock->quantity,
                    'min'  => $stock->minimum_stock,
                    'exp'  => $stock->expiry_date?->format('d/m/Y'),
                    'type' => $type,
                ];
            });

        return array_merge($common, [
            'total_users'       => User::count(),
            'total_patients'    => Patient::count(),
            'visits_today'      => $totalToday,
            'failed_jobs'       => DB::table('failed_jobs')->count(),
            'users_by_role'     => User::select('role', DB::raw('count(*) as total'))
                                       ->groupBy('role')
                                       ->pluck('total', 'role'),
            'totalToday'        => $totalToday,
            'totalSelesai'      => $totalSelesai,
            'totalBelumSelesai' => $totalBelumSelesai,
            'rmePercent'        => $rmePercent,
            'resepPercent'      => $resepPercent,
            'resumePercent'     => $resumePercent,
            'highAlertDrugs'    => $highAlertDrugs,
            'poliProgress'      => $common['poliProgress'],
        ]);
    }

    private function dokterData(User $user): array
    {
        $today = Carbon::today();
        $common = $this->commonTodayStats();

        $doctor = \App\Models\Doctor::where('user_id', $user->id)->first();

        $visitsQuery = Visit::with('patient', 'poli')
            ->whereDate('tanggal_kunjungan', $today);

        if ($doctor) {
            $visitsQuery->where('doctor_id', $doctor->id);
        }

        $visits = $visitsQuery->orderBy('created_at')->get();

        $pendingLab = LabRequest::where('requested_by', $user->id)
            ->where('status', 'completed')
            ->whereDoesntHave('result')
            ->with('visit.patient')
            ->latest()
            ->take(10)
            ->get();

        $pendingRadiology = RadiologyRequest::where('requested_by', $user->id)
            ->where('status', 'completed')
            ->whereDoesntHave('result')
            ->with('visit.patient')
            ->latest()
            ->take(10)
            ->get();

        $queue = QueueEntry::with('visit.patient', 'poli')
            ->whereHas('visit', fn($q) => $q->whereDate('tanggal_kunjungan', $today))
            ->whereIn('status', ['waiting', 'called'])
            ->orderBy('queue_number')
            ->get();

        return array_merge($common, compact('visits', 'queue', 'pendingLab', 'pendingRadiology'));
    }

    private function perawatData(): array
    {
        $today = Carbon::today();
        $common = $this->commonTodayStats();

        $queue = QueueEntry::with('visit.patient', 'poli')
            ->whereHas('visit', fn($q) => $q->whereDate('tanggal_kunjungan', $today))
            ->whereIn('status', ['waiting', 'called', 'in_examination'])
            ->orderBy('queue_number')
            ->get();

        $beds = Bed::with('room', 'currentPatient')
            ->orderBy('status')
            ->get();

        $bedStats = [
            'available'   => $beds->where('status', 'available')->count(),
            'occupied'    => $beds->where('status', 'occupied')->count(),
            'maintenance' => $beds->where('status', 'maintenance')->count(),
            'inactive'    => $beds->where('status', 'inactive')->count(),
        ];

        return array_merge($common, compact('queue', 'beds', 'bedStats'));
    }

    private function farmasiData(): array
    {
        $common = $this->commonTodayStats();

        $prescriptions = Prescription::with('visit.patient')
            ->whereIn('status', ['pending', 'validated'])
            ->latest()
            ->take(20)
            ->get();

        $lowStock = DrugStock::with('drug')
            ->whereColumn('quantity', '<=', 'minimum_stock')
            ->get();

        $nearExpiry = DrugStock::with('drug')
            ->where('expiry_date', '>', Carbon::today())
            ->where('expiry_date', '<=', Carbon::today()->addDays(30))
            ->get();

        $expired = DrugStock::with('drug')
            ->where('expiry_date', '<', Carbon::today())
            ->get();

        return array_merge($common, compact('prescriptions', 'lowStock', 'nearExpiry', 'expired'));
    }

    private function kasirData(): array
    {
        $common = $this->commonTodayStats();

        $pendingBills = Bill::with('visit.patient')
            ->where('status', 'pending')
            ->latest()
            ->take(20)
            ->get();

        $bpjsClaims = Bill::with('visit.patient')
            ->where('payment_method', 'bpjs')
            ->whereNotNull('bpjs_claim_status')
            ->latest()
            ->take(20)
            ->get();

        $claimStats = [
            'pending'   => Bill::where('payment_method', 'bpjs')->where('bpjs_claim_status', 'pending')->count(),
            'submitted' => Bill::where('payment_method', 'bpjs')->where('bpjs_claim_status', 'submitted')->count(),
            'approved'  => Bill::where('payment_method', 'bpjs')->where('bpjs_claim_status', 'approved')->count(),
            'rejected'  => Bill::where('payment_method', 'bpjs')->where('bpjs_claim_status', 'rejected')->count(),
        ];

        return array_merge($common, compact('pendingBills', 'bpjsClaims', 'claimStats'));
    }

    private function petugasPendaftaranData(): array
    {
        $today = Carbon::today();
        $common = $this->commonTodayStats();

        $queue = QueueEntry::with('visit.patient', 'poli')
            ->whereHas('visit', fn($q) => $q->whereDate('tanggal_kunjungan', $today))
            ->orderBy('queue_number')
            ->get();

        $visitsToday = $common['total'];

        return array_merge($common, compact('queue', 'visitsToday'));
    }

    private function manajemenData(): array
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $common    = $this->commonTodayStats();

        $totalToday        = $common['total'];
        $totalSelesai      = $common['selesai'];
        $totalBelumSelesai = $common['belumSelesai'];
        $rmePercent        = $common['persenRme'];
        $resepPercent      = $common['persenResep'];
        $resumePercent     = $common['persenResume'];

        $visitStats = [
            'today'       => $totalToday,
            'this_month'  => Visit::where('tanggal_kunjungan', '>=', $thisMonth)->count(),
            'by_penjamin' => Visit::select('jenis_penjamin', DB::raw('count(*) as total'))
                                  ->where('tanggal_kunjungan', '>=', $thisMonth)
                                  ->groupBy('jenis_penjamin')
                                  ->pluck('total', 'jenis_penjamin'),
        ];

        $financialStats = [
            'paid_this_month' => Bill::where('status', 'paid')
                                     ->where('created_at', '>=', $thisMonth)
                                     ->sum('total_amount'),
            'pending_amount'  => Bill::where('status', 'pending')->sum('total_amount'),
            'bpjs_submitted'  => Bill::where('payment_method', 'bpjs')
                                     ->where('bpjs_claim_status', 'submitted')
                                     ->sum('total_amount'),
        ];

        $highAlertDrugs = \App\Models\DrugStock::with('drug')
            ->where(function ($q) {
                $q->whereColumn('quantity', '<=', 'minimum_stock')
                  ->orWhere('expiry_date', '<', Carbon::today())
                  ->orWhere(function ($q2) {
                      $q2->where('expiry_date', '>', Carbon::today())
                         ->where('expiry_date', '<=', Carbon::today()->addDays(30));
                  });
            })
            ->get()
            ->map(function ($stock) {
                $type = 'low';
                if ($stock->expiry_date && $stock->expiry_date->lt(Carbon::today())) {
                    $type = 'expired';
                } elseif ($stock->expiry_date && $stock->expiry_date->lte(Carbon::today()->addDays(30))) {
                    $type = 'near_expiry';
                }
                return [
                    'nama' => $stock->drug?->nama ?? '-',
                    'qty'  => $stock->quantity,
                    'min'  => $stock->minimum_stock,
                    'exp'  => $stock->expiry_date?->format('d/m/Y'),
                    'type' => $type,
                ];
            });

        return array_merge($common, compact(
            'visitStats', 'financialStats',
            'totalToday', 'totalSelesai', 'totalBelumSelesai',
            'rmePercent', 'resepPercent', 'resumePercent',
            'highAlertDrugs'
        ), ['poliProgress' => $common['poliProgress']]);
    }
}
