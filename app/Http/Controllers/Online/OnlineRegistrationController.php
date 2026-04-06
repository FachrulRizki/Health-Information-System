<?php

namespace App\Http\Controllers\Online;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use App\Models\Poli;
use App\Models\QueueEntry;
use App\Models\Visit;
use App\Services\Integration\BPJSService;
use App\Services\NotificationService;
use App\Services\PatientService;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class OnlineRegistrationController extends Controller
{
    public function __construct(
        private PatientService $patientService,
        private QueueService $queueService,
        private NotificationService $notificationService,
        private BPJSService $bpjsService,
    ) {}

    public function index()
    {
        $polis = Poli::where('is_active', true)->orderBy('nama_poli')->get();
        return view('online.register', compact('polis'));
    }

    public function getSchedules(Request $request)
    {
        $request->validate([
            'poli_id' => 'required|exists:polis,id',
            'tanggal' => 'required|date|after_or_equal:today',
        ]);

        $poliId  = (int) $request->poli_id;
        $tanggal = Carbon::parse($request->tanggal);
        $hari    = $this->getDayName($tanggal->dayOfWeek);

        $schedules = DoctorSchedule::with('doctor')
            ->where('poli_id', $poliId)
            ->where('hari', $hari)
            ->where('is_active', true)
            ->get()
            ->map(function (DoctorSchedule $schedule) use ($tanggal) {
                $filled    = QueueEntry::whereHas('visit', fn($q) => $q->whereDate('tanggal_kunjungan', $tanggal->toDateString())->where('poli_id', $schedule->poli_id)->where('doctor_id', $schedule->doctor_id))->count();
                $available = max(0, $schedule->kuota - $filled);
                return [
                    'schedule_id' => $schedule->id,
                    'doctor_id'   => $schedule->doctor_id,
                    'doctor_name' => $schedule->doctor->nama_dokter,
                    'jam_mulai'   => $schedule->jam_mulai,
                    'jam_selesai' => $schedule->jam_selesai,
                    'kuota'       => $schedule->kuota,
                    'filled'      => $filled,
                    'available'   => $available,
                    'is_full'     => $available === 0,
                ];
            });

        $alternatives = [];
        if ($schedules->every(fn($s) => $s['is_full'])) {
            $alternatives = $this->getAlternativeSchedules($poliId, $tanggal);
        }

        return response()->json(['schedules' => $schedules, 'alternatives' => $alternatives]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'      => 'required|string|max:200',
            'tanggal_lahir'     => 'required|date|before:today',
            'nik'               => 'nullable|string|max:16',
            'no_telepon'        => 'nullable|string|max:20',
            'jenis_penjamin'    => 'required|in:umum,bpjs,asuransi',
            'no_bpjs'           => 'required_if:jenis_penjamin,bpjs|nullable|string|max:20',
            'no_polis_asuransi' => 'required_if:jenis_penjamin,asuransi|nullable|string|max:50',
            'nama_asuransi'     => 'required_if:jenis_penjamin,asuransi|nullable|string|max:100',
            'poli_id'           => 'required|exists:polis,id',
            'doctor_id'         => 'nullable|exists:doctors,id',
            'tanggal_kunjungan' => 'required|date|after_or_equal:today',
        ]);

        $slotError = $this->checkSlotAvailability((int) $validated['poli_id'], isset($validated['doctor_id']) && $validated['doctor_id'] ? (int) $validated['doctor_id'] : null, $validated['tanggal_kunjungan']);
        if ($slotError) {
            return back()->withInput()->withErrors(['slot' => $slotError]);
        }

        // Requirement 18.5: validate BPJS peserta before confirming registration
        if ($validated['jenis_penjamin'] === 'bpjs') {
            $noKartu = $validated['no_bpjs'];
            try {
                $bpjsResult = $this->bpjsService->validatePeserta($noKartu);
                $statusKode = $bpjsResult['response']['peserta']['statusPeserta']['kode'] ?? null;
                if ($statusKode !== '1') {
                    $statusKeterangan = $bpjsResult['response']['peserta']['statusPeserta']['keterangan']
                        ?? ($bpjsResult['message'] ?? 'Status peserta tidak aktif');
                    return back()->withInput()->withErrors([
                        'no_bpjs' => "Validasi BPJS gagal: {$statusKeterangan}. Silakan daftar sebagai pasien umum atau hubungi BPJS.",
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('OnlineRegistration: BPJS validation error', ['no_bpjs' => $noKartu, 'error' => $e->getMessage()]);
                return back()->withInput()->withErrors([
                    'no_bpjs' => 'Validasi BPJS tidak dapat dilakukan saat ini. Silakan coba lagi atau hubungi petugas.',
                ]);
            }
        }

        $patient = $this->patientService->createPatient([
            'nama_lengkap'      => $validated['nama_lengkap'],
            'tanggal_lahir'     => $validated['tanggal_lahir'],
            'jenis_kelamin'     => 'L',
            'nik'               => $validated['nik'] ?? null,
            'no_telepon'        => $validated['no_telepon'] ?? null,
            'jenis_penjamin'    => $validated['jenis_penjamin'],
            'no_bpjs'           => $validated['no_bpjs'] ?? null,
            'no_polis_asuransi' => $validated['no_polis_asuransi'] ?? null,
            'nama_asuransi'     => $validated['nama_asuransi'] ?? null,
        ]);

        // Use a system user ID (1 = admin) for online registrations
        $visit = $this->patientService->createVisit($patient->id, [
            'poli_id'           => $validated['poli_id'],
            'doctor_id'         => $validated['doctor_id'] ?? null,
            'user_id'           => 1,
            'jenis_penjamin'    => $validated['jenis_penjamin'],
            'status'            => 'pendaftaran',
            'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
        ]);

        $this->queueService->assignQueue($visit->id, (int) $validated['poli_id']);

        // Reload visit with relations needed for notification
        $visit->load(['patient', 'poli', 'queueEntry']);

        // Determine notification channel from request (default: email)
        $channel = $request->input('notification_channel', 'email');
        $this->notificationService->sendRegistrationConfirmation($visit, $channel);

        return redirect()->route('online.success', ['noRawat' => $visit->no_rawat]);
    }

    public function success(string $noRawat)
    {
        $visit = Visit::with(['patient', 'poli', 'queueEntry'])->where('no_rawat', $noRawat)->firstOrFail();
        return view('online.success', compact('visit'));
    }

    private function getDayName(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'minggu', 1 => 'senin', 2 => 'selasa', 3 => 'rabu',
            4 => 'kamis', 5 => 'jumat', 6 => 'sabtu',
        };
    }

    private function checkSlotAvailability(int $poliId, ?int $doctorId, string $tanggal): ?string
    {
        $hari     = $this->getDayName(Carbon::parse($tanggal)->dayOfWeek);
        $schedule = DoctorSchedule::where('poli_id', $poliId)->where('hari', $hari)->where('is_active', true)->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))->first();

        if (! $schedule) return 'Tidak ada jadwal praktik pada tanggal tersebut.';

        $filled = QueueEntry::whereHas('visit', fn($q) => $q->whereDate('tanggal_kunjungan', $tanggal)->where('poli_id', $poliId)->when($doctorId, fn($q2) => $q2->where('doctor_id', $doctorId)))->count();

        if ($filled >= $schedule->kuota) return 'Slot antrian pada jadwal ini sudah penuh. Silakan pilih jadwal lain.';

        return null;
    }

    private function getAlternativeSchedules(int $poliId, Carbon $fromDate): array
    {
        $alternatives = [];
        for ($i = 1; $i <= 7; $i++) {
            $date = $fromDate->copy()->addDays($i);
            $hari = $this->getDayName($date->dayOfWeek);
            foreach (DoctorSchedule::with('doctor')->where('poli_id', $poliId)->where('hari', $hari)->where('is_active', true)->get() as $schedule) {
                $filled = QueueEntry::whereHas('visit', fn($q) => $q->whereDate('tanggal_kunjungan', $date->toDateString())->where('poli_id', $poliId)->where('doctor_id', $schedule->doctor_id))->count();
                if ($filled < $schedule->kuota) {
                    $alternatives[] = ['tanggal' => $date->toDateString(), 'hari' => $hari, 'doctor_name' => $schedule->doctor->nama_dokter, 'jam_mulai' => $schedule->jam_mulai, 'jam_selesai' => $schedule->jam_selesai, 'available' => $schedule->kuota - $filled];
                }
            }
            if (count($alternatives) >= 3) break;
        }
        return $alternatives;
    }
}
