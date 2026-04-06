<?php

namespace App\Services;

use App\Events\QueueStatusUpdated;
use App\Models\QueueEntry;
use App\Models\Visit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class QueueService
{
    public function assignQueue(int $visitId, int $poliId): QueueEntry
    {
        $today = Carbon::today()->toDateString();

        $queueNumber = QueueEntry::whereHas('visit', fn($q) => $q->whereDate('tanggal_kunjungan', $today))
            ->where('poli_id', $poliId)
            ->count() + 1;

        $entry = QueueEntry::create([
            'visit_id'     => $visitId,
            'poli_id'      => $poliId,
            'queue_number' => $queueNumber,
            'status'       => 'menunggu',
        ]);

        // Update visit status to 'menunggu' after queue is assigned (Req 4.5)
        Visit::where('id', $visitId)->update(['status' => 'menunggu']);

        event(new QueueStatusUpdated($entry));

        return $entry;
    }

    public function updateStatus(int $queueId, string $status): QueueEntry
    {
        $entry = QueueEntry::findOrFail($queueId);
        $entry->update(['status' => $status]);

        // Map queue status to visit status (Req 4.5: Pendaftaran → Antrian → Pemeriksaan → Farmasi → Kasir → Selesai)
        $visitStatus = match ($status) {
            'menunggu'          => 'menunggu',
            'dipanggil'         => 'dipanggil',
            'dalam_pemeriksaan' => 'dalam_pemeriksaan',
            'selesai'           => 'selesai',
            default             => null,
        };

        if ($visitStatus !== null) {
            Visit::where('id', $entry->visit_id)->update(['status' => $visitStatus]);
        }

        event(new QueueStatusUpdated($entry->fresh()));

        return $entry->fresh();
    }

    public function getQueueByPoli(int $poliId, ?string $date = null): Collection
    {
        $date = $date ?? Carbon::today()->toDateString();

        return QueueEntry::with(['visit.patient'])
            ->where('poli_id', $poliId)
            ->whereHas('visit', fn($q) => $q->whereDate('tanggal_kunjungan', $date))
            ->orderBy('queue_number')
            ->get();
    }
}
