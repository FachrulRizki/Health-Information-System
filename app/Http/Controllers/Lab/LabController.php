<?php
namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Services\LabService;
use Illuminate\Http\Request;

class LabController extends Controller
{
    public function __construct(private LabService $labService) {}

    public function index()
    {
        $rawatJalan = LabRequest::with(['visit.patient', 'examinationType'])
            ->whereHas('visit', fn($q) => $q->whereNull('inpatient_records.id')
                ->leftJoin('inpatient_records', 'inpatient_records.visit_id', '=', 'visits.id'))
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'rj_page');

        $rawatInap = LabRequest::with(['visit.patient', 'examinationType'])
            ->whereHas('visit', fn($q) => $q->whereHas('inpatientRecord'))
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'ri_page');

        return view('lab.index', compact('rawatJalan', 'rawatInap'));
    }

    public function show(int $labRequestId)
    {
        $labRequest = LabRequest::with(['visit.patient', 'visit.poli', 'visit.doctor', 'examinationType', 'result'])
            ->findOrFail($labRequestId);
        return view('lab.show', compact('labRequest'));
    }

    public function storeResult(Request $request, int $labRequestId)
    {
        $request->validate([
            'result_data' => 'required|string',
        ]);

        $labRequest = LabRequest::findOrFail($labRequestId);

        LabResult::updateOrCreate(
            ['lab_request_id' => $labRequestId],
            ['result_data' => $request->result_data, 'created_by' => auth()->id()]
        );

        $labRequest->update(['status' => 'completed']);

        return redirect()->route('lab.index')->with('success', 'Hasil laboratorium berhasil disimpan.');
    }
}
