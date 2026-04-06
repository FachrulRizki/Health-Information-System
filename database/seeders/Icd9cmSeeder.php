<?php

namespace Database\Seeders;

use App\Models\Icd9cmCode;
use Illuminate\Database\Seeder;

class Icd9cmSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            ['kode' => '89.01', 'deskripsi' => 'Anamnesis dan evaluasi, didefinisikan'],
            ['kode' => '89.7',  'deskripsi' => 'Pemeriksaan laboratorium'],
            ['kode' => '87.44', 'deskripsi' => 'Foto toraks rutin'],
            ['kode' => '88.72', 'deskripsi' => 'Ultrasonografi diagnostik abdomen'],
            ['kode' => '89.52', 'deskripsi' => 'Elektrokardiogram'],
            ['kode' => '93.35', 'deskripsi' => 'Pemasangan infus intravena'],
            ['kode' => '99.18', 'deskripsi' => 'Injeksi atau infus elektrolit'],
            ['kode' => '96.04', 'deskripsi' => 'Pemasangan kateter urin'],
            ['kode' => '57.94', 'deskripsi' => 'Pemasangan kateter suprapubik'],
            ['kode' => '99.29', 'deskripsi' => 'Injeksi atau infus zat terapeutik lainnya'],
        ];

        foreach ($codes as $code) {
            Icd9cmCode::updateOrCreate(['kode' => $code['kode']], $code);
        }
    }
}
