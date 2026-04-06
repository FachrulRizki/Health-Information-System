<?php

namespace Database\Seeders;

use App\Models\Poli;
use Illuminate\Database\Seeder;

class PoliSeeder extends Seeder
{
    public function run(): void
    {
        $polis = [
            ['kode_poli' => 'POL-UMM', 'nama_poli' => 'Poli Umum',      'is_active' => true],
            ['kode_poli' => 'POL-GIG', 'nama_poli' => 'Poli Gigi',      'is_active' => true],
            ['kode_poli' => 'POL-ANK', 'nama_poli' => 'Poli Anak',      'is_active' => true],
            ['kode_poli' => 'POL-KAN', 'nama_poli' => 'Poli Kandungan', 'is_active' => true],
            ['kode_poli' => 'POL-IGD', 'nama_poli' => 'IGD',            'is_active' => true],
        ];

        foreach ($polis as $poli) {
            Poli::updateOrCreate(['kode_poli' => $poli['kode_poli']], $poli);
        }
    }
}
