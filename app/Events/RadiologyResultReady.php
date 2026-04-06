<?php

namespace App\Events;

use App\Models\RadiologyResult;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RadiologyResultReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly RadiologyResult $radiologyResult) {}

    public function broadcastOn(): Channel
    {
        $requestedBy = $this->radiologyResult->radiologyRequest->requested_by;
        return new PrivateChannel("user.{$requestedBy}");
    }

    public function broadcastWith(): array
    {
        $radiologyRequest = $this->radiologyResult->radiologyRequest->load(['visit.patient', 'examinationType']);
        $visit            = $radiologyRequest->visit;
        $patient          = $visit->patient;

        return [
            'radiology_request_id' => $radiologyRequest->id,
            'visit_id'             => $visit->id,
            'patient_name'         => $patient->nama_lengkap,
            'examination_type'     => $radiologyRequest->examinationType?->nama ?? '',
            'result_notes'         => $this->radiologyResult->result_notes,
        ];
    }
}
