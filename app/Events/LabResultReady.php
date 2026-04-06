<?php

namespace App\Events;

use App\Models\LabResult;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LabResultReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly LabResult $labResult) {}

    public function broadcastOn(): Channel
    {
        $requestedBy = $this->labResult->labRequest->requested_by;
        return new PrivateChannel("user.{$requestedBy}");
    }

    public function broadcastWith(): array
    {
        $labRequest      = $this->labResult->labRequest->load(['visit.patient', 'examinationType']);
        $visit           = $labRequest->visit;
        $patient         = $visit->patient;

        return [
            'lab_request_id'   => $labRequest->id,
            'visit_id'         => $visit->id,
            'patient_name'     => $patient->nama_lengkap,
            'examination_type' => $labRequest->examinationType?->nama ?? '',
            'result_summary'   => $this->labResult->result_data,
        ];
    }
}
