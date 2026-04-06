<?php
namespace App\Http\Controllers\Radiology;

use App\Http\Controllers\Controller;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use App\Services\RadiologyService;
use Illuminate\Http\Request;

class RadiologyController extends Controller
{
    public function __construct(private RadiologyService $radiologyService) {}

    public function index()
    {
        $rawatJalan = RadiologyRequest::with(['visit.patient', 'examinationType'])
            ->whereHas('visit', fn($q) => $q->whereDoesntHave('inpatientRecord'))
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'rj_page');

        $rawatInap = RadiologyRequest::with(['visit.patient', 'examinationType'])
            ->whereHas('visit', fn($q) => $q->whereHas('inpatientRecord'))
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'ri_page');

        return view('radiology.index', compact('rawatJalan', 'rawatInap'));
    }

    public function show(int $radiologyRequestId)
    {
        $radiologyRequest = RadiologyRequest::with(['visit.patient', 'visit.poli', 'visit.doctor', 'examinationType', 'result'])
            ->findOrFail($radiologyRequestId);
        return view('radiology.show', compact('radiologyRequest'));
    }

    public function storeResult(Request $request, int $radiologyRequestId)
    {
        $request->validate([
            'result_notes' => 'required|string',
            'file'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $radiologyRequest = RadiologyRequest::findOrFail($radiologyRequestId);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('radiology-results', 'public');
        }

        RadiologyResult::updateOrCreate(
            ['radiology_request_id' => $radiologyRequestId],
            [
                'result_notes' => $request->result_notes,
                'file_path'    => $filePath,
                'created_by'   => auth()->id(),
            ]
        );

        $radiologyRequest->update(['status' => 'completed']);

        return redirect()->route('radiology.index')->with('success', 'Hasil radiologi berhasil disimpan.');
    }
}
