<?php

namespace App\Http\Controllers\Inpatient;

use App\Http\Controllers\Controller;
use App\Models\InpatientRecord;
use App\Models\Visit;
use App\Services\BedManagementService;
use App\Services\RMEService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InpatientController extends Controller
{
    public function __construct(
        private RMEService $rmeService,
        private BedManagementService $bedManagementService,
    ) {}

    public function index(): View
    {
        $records = InpatientRecord::with(['visit.patient', 'visit.poli', 'visit.doctor', 'bed.room'])
            ->where('status_pulang', 'dirawat')
            ->orderByDesc('tanggal_masuk')
            ->get();

        return view('inpatient.index', compact('records'));
    }

    public function show(int $visitId): View
    {
        $visit = Visit::with([
            'patient', 'poli', 'doctor', 'medicalRecord',
            'inpatientRecord.bed.room',
            'diagnoses.icd10Code', 'procedures.icd9cmCode',
        ])->findOrFail($visitId);

        $history          = $this->rmeService->getPatientHistory($visit->patient_id);
        $drugs            = \App\Models\Drug::where('is_active', true)->orderBy('nama')->get();
        $examinationTypes = \App\Models\ExaminationType::orderBy('nama')->get();
        $actionMasters    = \App\Models\ActionMaster::orderBy('nama')->get();

        return view('inpatient.show', compact('visit', 'history', 'drugs', 'examinationTypes', 'actionMasters'));
    }

    public function store(Request $request, int $visitId): RedirectResponse
    {
        $request->validate([
            'subjective'   => 'nullable|string',
            'objective'    => 'nullable|string',
            'assessment'   => 'nullable|string',
            'plan'         => 'nullable|string',
            'diagnoses'    => 'required|array|min:1',
            'diagnoses.*'  => 'required|string|exists:icd10_codes,kode',
            'procedures'   => 'nullable|array',
            'procedures.*' => 'nullable|string|exists:icd9cm_codes,kode',
        ]);

        $this->rmeService->saveSOAP($visitId, $request->only([
            'subjective', 'objective', 'assessment', 'plan', 'diagnoses', 'procedures',
        ]));

        return redirect()->route('inpatient.show', $visitId)->with('success', 'SOAP rawat inap berhasil disimpan.');
    }

    public function updateNotes(Request $request, int $visitId): RedirectResponse
    {
        $request->validate([
            'catatan_keperawatan' => 'nullable|string',
            'penilaian_medis'     => 'nullable|string',
            'asesmen_awal'        => 'nullable|string',
        ]);

        $visit  = Visit::findOrFail($visitId);
        $record = $visit->inpatientRecord;

        if (! $record) {
            return back()->withErrors(['error' => 'Data rawat inap tidak ditemukan.']);
        }

        $record->update($request->only(['catatan_keperawatan', 'penilaian_medis', 'asesmen_awal']));

        return redirect()->route('inpatient.show', $visitId)->with('success', 'Catatan keperawatan berhasil diperbarui.');
    }

    public function discharge(Request $request, int $visitId): RedirectResponse
    {
        $request->validate([
            'status_pulang'  => 'required|in:pulang_atas_permintaan,pulang_sembuh,meninggal,dirujuk',
            'tanggal_keluar' => 'required|date',
            'resume_pulang'  => 'nullable|string',
        ]);

        $visit  = Visit::findOrFail($visitId);
        $record = $visit->inpatientRecord;

        if (! $record) {
            return back()->withErrors(['error' => 'Data rawat inap tidak ditemukan.']);
        }

        $record->update([
            'status_pulang'  => $request->status_pulang,
            'tanggal_keluar' => $request->tanggal_keluar,
            'resume_pulang'  => $request->resume_pulang,
        ]);

        return redirect()->route('inpatient.index')->with('success', 'Pasien berhasil dipulangkan.');
    }

    public function beds(): View
    {
        $rooms = $this->bedManagementService->getBedMap();

        return view('inpatient.beds', compact('rooms'));
    }
}
