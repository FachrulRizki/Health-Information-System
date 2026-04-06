<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Visit;
use App\Services\Integration\BPJSService;
use App\Services\PatientService;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    public function __construct(
        private PatientService $patientService,
        private QueueService $queueService,
        private BPJSService $bpjsService,
    ) {}

    public function index(Request $request)
    {
        $q        = $request->query('q', '');
        $dateFrom = $request->query('date_from', today()->toDateString());
        $dateTo   = $request->query('date_to', today()->toDateString());

        if ($q !== '') {
            $patients    = $this->patientService->searchPatients($q);
            $todayVisits = collect();
        } else {
            $patients    = collect();
            $todayVisits = Visit::with(['patient', 'poli', 'doctor', 'queueEntry'])
                ->whereDate('tanggal_kunjungan', '>=', $dateFrom)
                ->whereDate('tanggal_kunjungan', '<=', $dateTo)
                ->orderBy('tanggal_kunjungan', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('registration.index', compact('q', 'patients', 'todayVisits', 'dateFrom', 'dateTo'));
    }

    public function show(int $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $visits  = Visit::with(['poli', 'doctor', 'medicalRecord', 'bill'])
            ->where('patient_id', $patientId)
            ->orderByDesc('tanggal_kunjungan')
            ->paginate(10);

        return view('registration.show', compact('patient', 'visits'));
    }

    public function create()
    {
        $polis   = Poli::where('is_active', true)->orderBy('nama_poli')->get();
        $doctors = Doctor::where('is_active', true)->orderBy('nama_dokter')->get();

        return view('registration.create', compact('polis', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'      => 'required|string|max:200',
            'tanggal_lahir'     => 'required|date|before:today',
            'jenis_kelamin'     => 'required|in:L,P',
            'alamat'            => 'nullable|string',
            'nik'               => 'nullable|string|max:16',
            'no_telepon'        => 'nullable|string|max:20',
            'jenis_penjamin'    => 'required|in:umum,bpjs,asuransi',
            'no_bpjs'           => 'required_if:jenis_penjamin,bpjs|nullable|string|max:20',
            'no_polis_asuransi' => 'required_if:jenis_penjamin,asuransi|nullable|string|max:50',
            'nama_asuransi'     => 'required_if:jenis_penjamin,asuransi|nullable|string|max:100',
            'poli_id'           => 'required|exists:polis,id',
            'doctor_id'         => 'nullable|exists:doctors,id',
            'tanggal_kunjungan' => 'required|date',
        ]);

        // BPJS validation (Req 3.5, 3.6)
        if ($validated['jenis_penjamin'] === 'bpjs') {
            $bpjsError = $this->validateBpjsPeserta($validated['no_bpjs'] ?? '');
            if ($bpjsError !== null && $request->input('bpjs_confirmed') !== '1') {
                return back()->withInput()->withErrors(['bpjs' => $bpjsError])->with('bpjs_inactive', true);
            }
        }

        $patient = $this->patientService->createPatient([
            'nama_lengkap'      => $validated['nama_lengkap'],
            'tanggal_lahir'     => $validated['tanggal_lahir'],
            'jenis_kelamin'     => $validated['jenis_kelamin'],
            'alamat'            => $validated['alamat'] ?? null,
            'nik'               => $validated['nik'] ?? null,
            'no_telepon'        => $validated['no_telepon'] ?? null,
            'jenis_penjamin'    => $validated['jenis_penjamin'],
            'no_bpjs'           => $validated['no_bpjs'] ?? null,
            'no_polis_asuransi' => $validated['no_polis_asuransi'] ?? null,
            'nama_asuransi'     => $validated['nama_asuransi'] ?? null,
        ]);

        $visit = $this->patientService->createVisit($patient->id, [
            'poli_id'           => $validated['poli_id'],
            'doctor_id'         => $validated['doctor_id'] ?? null,
            'user_id'           => auth()->id(),
            'jenis_penjamin'    => $validated['jenis_penjamin'],
            'status'            => 'pendaftaran',
            'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
        ]);

        if ($validated['jenis_penjamin'] === 'bpjs') {
            $this->insertSepForVisit($visit, $validated);
        }

        $this->queueService->assignQueue($visit->id, (int) $validated['poli_id']);

        return redirect()->route('registration.index')
            ->with('success', "Pasien {$patient->nama_lengkap} berhasil didaftarkan. NoRM: {$patient->no_rm}");
    }

    public function createVisit(Request $request, int $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $polis   = Poli::where('is_active', true)->orderBy('nama_poli')->get();
        $doctors = Doctor::where('is_active', true)->orderBy('nama_dokter')->get();

        return view('registration.create-visit', compact('patient', 'polis', 'doctors'));
    }

    public function storeVisit(Request $request, int $patientId)
    {
        $patient   = Patient::findOrFail($patientId);
        $validated = $request->validate([
            'jenis_penjamin'    => 'required|in:umum,bpjs,asuransi',
            'no_bpjs'           => 'required_if:jenis_penjamin,bpjs|nullable|string|max:20',
            'poli_id'           => 'required|exists:polis,id',
            'doctor_id'         => 'nullable|exists:doctors,id',
            'tanggal_kunjungan' => 'required|date',
        ]);

        // BPJS validation (Req 3.5, 3.6)
        if ($validated['jenis_penjamin'] === 'bpjs') {
            $bpjsError = $this->validateBpjsPeserta($validated['no_bpjs'] ?? '');
            if ($bpjsError !== null && $request->input('bpjs_confirmed') !== '1') {
                return back()->withInput()->withErrors(['bpjs' => $bpjsError])->with('bpjs_inactive', true);
            }
        }

        $visit = $this->patientService->createVisit($patient->id, [
            'poli_id'           => $validated['poli_id'],
            'doctor_id'         => $validated['doctor_id'] ?? null,
            'user_id'           => auth()->id(),
            'jenis_penjamin'    => $validated['jenis_penjamin'],
            'status'            => 'pendaftaran',
            'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
        ]);

        if ($validated['jenis_penjamin'] === 'bpjs') {
            $this->insertSepForVisit($visit, $validated);
        }

        $this->queueService->assignQueue($visit->id, (int) $validated['poli_id']);

        return redirect()->route('registration.index')
            ->with('success', "Kunjungan untuk {$patient->nama_lengkap} berhasil didaftarkan. NoRawat: {$visit->no_rawat}");
    }

    private function validateBpjsPeserta(string $noKartu): ?string
    {
        try {
            $result = $this->bpjsService->validatePeserta($noKartu);
            $kode   = $result['response']['peserta']['statusPeserta']['kode']
                ?? $result['peserta']['statusPeserta']['kode']
                ?? null;
            if ($kode !== null && $kode !== '1') {
                $status = $result['response']['peserta']['statusPeserta']['keterangan']
                    ?? $result['peserta']['statusPeserta']['keterangan']
                    ?? 'Tidak Aktif';
                return "Status peserta BPJS tidak aktif: {$status}. Konfirmasi untuk melanjutkan sebagai pasien umum.";
            }
        } catch (\Exception $e) {
            Log::warning('BPJS validatePeserta failed, proceeding', ['error' => $e->getMessage()]);
        }
        return null;
    }

    private function insertSepForVisit(Visit $visit, array $validated): void
    {
        try {
            $result = $this->bpjsService->insertSEP([
                'no_rawat'          => $visit->no_rawat,
                'no_bpjs'           => $validated['no_bpjs'] ?? '',
                'poli_id'           => $validated['poli_id'],
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
            ]);
            $noSep = $result['sep']['noSep'] ?? $result['noSep'] ?? null;
            if ($noSep) {
                $visit->update(['no_sep' => $noSep]);
            }
        } catch (\Exception $e) {
            Log::warning('BPJS insertSEP failed, registration still completed', [
                'visit_id' => $visit->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
