<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Room;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            for ($i = 1; $i <= 5; $i++) {
                $kodeBed = $room->kode_kamar . '-B' . str_pad($i, 2, '0', STR_PAD_LEFT);
                Bed::updateOrCreate(
                    ['kode_bed' => $kodeBed],
                    [
                        'room_id'            => $room->id,
                        'kode_bed'           => $kodeBed,
                        'status'             => 'tersedia',
                        'current_patient_id' => null,
                    ]
                );
            }
        }
    }
}
