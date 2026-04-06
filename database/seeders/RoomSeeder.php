<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['kode_kamar' => 'KMR-1', 'nama_kamar' => 'Kelas 1', 'kelas' => '1', 'kapasitas' => 5, 'is_active' => true],
            ['kode_kamar' => 'KMR-2', 'nama_kamar' => 'Kelas 2', 'kelas' => '2', 'kapasitas' => 5, 'is_active' => true],
            ['kode_kamar' => 'KMR-3', 'nama_kamar' => 'Kelas 3', 'kelas' => '3', 'kapasitas' => 5, 'is_active' => true],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(['kode_kamar' => $room['kode_kamar']], $room);
        }
    }
}
