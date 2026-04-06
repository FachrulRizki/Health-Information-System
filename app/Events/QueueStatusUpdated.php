<?php

namespace App\Events;

use App\Models\QueueEntry;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly QueueEntry $queueEntry) {}

    public function broadcastOn(): Channel
    {
        return new Channel('poli.' . $this->queueEntry->poli_id);
    }

    public function broadcastWith(): array
    {
        return [
            'queue_id'     => $this->queueEntry->id,
            'visit_id'     => $this->queueEntry->visit_id,
            'poli_id'      => $this->queueEntry->poli_id,
            'queue_number' => $this->queueEntry->queue_number,
            'status'       => $this->queueEntry->status,
            'patient_name' => $this->queueEntry->visit?->patient?->nama_lengkap,
        ];
    }
}
