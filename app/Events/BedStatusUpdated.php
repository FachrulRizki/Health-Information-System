<?php

namespace App\Events;

use App\Models\Bed;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BedStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Bed $bed) {}

    public function broadcastOn(): Channel
    {
        return new Channel('beds');
    }

    public function broadcastWith(): array
    {
        return [
            'bed_id'       => $this->bed->id,
            'room_id'      => $this->bed->room_id,
            'kode_bed'     => $this->bed->kode_bed,
            'status'       => $this->bed->status,
            'patient_name' => $this->bed->currentPatient?->nama_lengkap,
        ];
    }
}
