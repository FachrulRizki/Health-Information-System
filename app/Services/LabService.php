<?php

namespace App\Services;

use App\Events\LabResultReady;
use App\Models\LabRequest;
use App\Models\LabResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class LabService
{
    public function createRequest(int $visitId, int $examinationTypeId): LabRequest
    {
        return LabRequest::create([
            'visit_id'            => $visitId,
            'examination_type_id' => $examinationTypeId,
            'status'              => 'pending',
            'requested_by'        => Auth::id(),
        ]);
    }

    public function takeSample(int $labRequestId, int $userId): LabRequest
    {
        $request = LabRequest::findOrFail($labRequestId);
        $request->update([
            'status'          => 'sample_taken',
            'sample_taken_at' => now(),
            'sample_taken_by' => $userId,
        ]);
        return $request->fresh();
    }

    public function saveResult(int $labRequestId, array $resultData, int $userId): LabResult
    {
        $result = LabResult::create([
            'lab_request_id' => $labRequestId,
            'result_data'    => $resultData,
            'created_by'     => $userId,
        ]);
        LabRequest::where('id', $labRequestId)->update(['status' => 'completed']);
        LabResultReady::dispatch($result);
        return $result;
    }

    public function getPendingRequests(): Collection
    {
        return LabRequest::with(['visit.patient', 'examinationType'])
            ->where('status', 'pending')
            ->get();
    }
}
