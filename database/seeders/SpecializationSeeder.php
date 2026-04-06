<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            ['kode' => 'SP-UMM', 'nama' => 'Umum'],
            ['kode' => 'SP-GIG', 'nama' => 'Gigi'],
            ['kode' => 'SP-ANK', 'nama' => 'Anak'],
            ['kode' => 'SP-KAN', 'nama' => 'Kandungan'],
            ['kode' => 'SP-BDH', 'nama' => 'Bedah'],
        ];

        foreach ($specializations as $spec) {
            Specialization::updateOrCreate(['kode' => $spec['kode']], $spec);
        }
    }
}
