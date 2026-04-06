<?php

namespace App\Services;

use App\Events\BedStatusUpdated;
use App\Jobs\SyncAplicareJob;
use App\Models\Bed;
use App\Models\InpatientRecord;
use App\Models\Room;
use App\Models\Visit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BedManagementService
{
    public function assignBed(int $visitId, int $bedId): InpatientRecord
    {
        return DB::transaction(function () use ($visitId, $bedId) {
            $visit = Visit::with('patient')->findOrFail($visitId);
            $bed   = Bed::findOrFail($bedId);

            $bed->update([
                'status'             => 'terisi',
                'current_patient_id' => $visit->patient_id,
            ]);

            $record = InpatientRecord::firstOrNew(['visit_id' => $visitId]);
            $record->fill([
                'bed_id'        => $bedId,
                'tanggal_masuk' => now()->toDateString(),
                'status_pulang' => 'dirawat',
            ]);
            $record->save();

            SyncAplicareJob::dispatch($bedId, 'terisi');
            BedStatusUpdated::dispatch($bed->fresh(['currentPatient']));

            return $record;
        });
    }

    public function releaseBed(int $bedId): void
    {
        $bed = Bed::findOrFail($bedId);

        $bed->update([
            'status'             => 'tersedia',
            'current_patient_id' => null,
        ]);

        SyncAplicareJob::dispatch($bedId, 'tersedia');
        BedStatusUpdated::dispatch($bed->fresh());
    }

    public function getBedMap(): Collection
    {
        return Room::with(['beds.currentPatient'])
            ->orderBy('nama_kamar')
            ->get();
    }
}
