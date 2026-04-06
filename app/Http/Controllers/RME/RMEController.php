<?php

namespace App\Http\Controllers\RME;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Services\RMEService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RMEController extends Controller
{
    public function __construct(private RMEService $rmeService) {}

    public function index(Request $request): View
    {
        $dateFrom = $request->query('date_from', today()->toDateString());
        $dateTo   = $request->query('date_to', today()->toDateString());

        // Rawat jalan: kunjungan yang TIDAK punya inpatientRecord (belum/tidak masuk rawat inap)
        $visits = Visit::with(['patient', 'poli', 'doctor', 'medicalRecord'])
            ->whereDoesntHave('inpatientRecord')
            ->whereDate('tanggal_kunjungan', '>=', $dateFrom)
            ->whereDate('tanggal_kunjungan', '<=', $dateTo)
            ->orderBy('tanggal_kunjungan', 'desc')
            ->orderBy('created_at')
            ->get();

        return view('rme.index', compact('visits', 'dateFrom', 'dateTo'));
    }

    public function show(int $visitId): View
    {
        $visit = Visit::with(['patient', 'poli', 'doctor', 'medicalRecord', 'diagnoses.icd10Code', 'procedures.icd9cmCode'])
            ->findOrFail($visitId);

        $history          = $this->rmeService->getPatientHistory($visit->patient_id);
        $drugs            = \App\Models\Drug::where('is_active', true)->orderBy('nama')->get();
        $drugUnits        = \App\Models\DrugUnit::orderBy('nama')->get();
        $examinationTypes = \App\Models\ExaminationType::orderBy('nama')->get();
        $actionMasters    = \App\Models\ActionMaster::orderBy('nama')->get();

        return view('rme.show', compact('visit', 'history', 'drugs', 'drugUnits', 'examinationTypes', 'actionMasters'));
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

        return redirect()->route('rme.show', $visitId)->with('success', 'Rekam medis berhasil disimpan.');
    }

    public function searchIcd10(Request $request): JsonResponse
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);
        return response()->json($this->rmeService->searchIcd10($q));
    }

    public function searchIcd9cm(Request $request): JsonResponse
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);
        return response()->json($this->rmeService->searchIcd9cm($q));
    }

    public function skdp(Request $request, int $visitId): JsonResponse
    {
        try {
            $data = $this->rmeService->createSKDP($visitId, $request->only([
                'tanggal_rencana_kontrol',
                'specialization_id',
                'dpjp_doctor_id',
            ]));

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }
    }
}
