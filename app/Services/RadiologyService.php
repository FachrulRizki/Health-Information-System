<?php

namespace App\Services;

use App\Events\RadiologyResultReady;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RadiologyService
{
    public function createRequest(int $visitId, int $examinationTypeId): RadiologyRequest
    {
        return RadiologyRequest::create([
            'visit_id'            => $visitId,
            'examination_type_id' => $examinationTypeId,
            'status'              => 'pending',
            'requested_by'        => Auth::id(),
        ]);
    }

    public function saveResult(int $radiologyRequestId, string $notes, ?string $filePath, int $userId): RadiologyResult
    {
        $result = RadiologyResult::create([
            'radiology_request_id' => $radiologyRequestId,
            'result_notes'         => $notes,
            'file_path'            => $filePath,
            'created_by'           => $userId,
        ]);
        RadiologyRequest::where('id', $radiologyRequestId)->update(['status' => 'completed']);
        RadiologyResultReady::dispatch($result);
        return $result;
    }

    public function getPendingRequests(): Collection
    {
        return RadiologyRequest::with(['visit.patient', 'examinationType'])
            ->where('status', 'pending')
            ->get();
    }
}
