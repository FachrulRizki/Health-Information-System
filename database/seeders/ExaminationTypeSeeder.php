<?php

namespace Database\Seeders;

use App\Models\ExaminationType;
use Illuminate\Database\Seeder;

class ExaminationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // Laboratorium
            ['kode' => 'LAB-DL',  'nama' => 'Darah Lengkap',          'kategori' => 'lab',       'is_active' => true],
            ['kode' => 'LAB-GDS', 'nama' => 'Gula Darah Sewaktu',     'kategori' => 'lab',       'is_active' => true],
            ['kode' => 'LAB-UR',  'nama' => 'Urinalisis',             'kategori' => 'lab',       'is_active' => true],
            ['kode' => 'LAB-LFT', 'nama' => 'Fungsi Hati (SGOT/SGPT)', 'kategori' => 'lab',     'is_active' => true],
            ['kode' => 'LAB-RFT', 'nama' => 'Fungsi Ginjal (Ureum/Kreatinin)', 'kategori' => 'lab', 'is_active' => true],
            // Radiologi
            ['kode' => 'RAD-TX',  'nama' => 'Foto Toraks',            'kategori' => 'radiologi', 'is_active' => true],
            ['kode' => 'RAD-ABD', 'nama' => 'Foto Abdomen',           'kategori' => 'radiologi', 'is_active' => true],
            // EKG
            ['kode' => 'EKG-12',  'nama' => 'EKG 12 Lead',            'kategori' => 'ekg',       'is_active' => true],
            // USG
            ['kode' => 'USG-ABD', 'nama' => 'USG Abdomen',            'kategori' => 'usg',       'is_active' => true],
            // CTG
            ['kode' => 'CTG-STD', 'nama' => 'CTG (Kardiotokografi)',   'kategori' => 'ctg',       'is_active' => true],
        ];

        foreach ($types as $type) {
            ExaminationType::updateOrCreate(['kode' => $type['kode']], $type);
        }
    }
}
