<?php

namespace Database\Seeders;

use App\Models\Icd10Code;
use Illuminate\Database\Seeder;

class Icd10Seeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            // Penyakit infeksi usus
            ['kode' => 'A00', 'deskripsi' => 'Kolera'],
            ['kode' => 'A01', 'deskripsi' => 'Demam tifoid dan paratifoid'],
            ['kode' => 'A09', 'deskripsi' => 'Diare dan gastroenteritis infeksi'],
            // Penyakit saluran napas atas
            ['kode' => 'J00', 'deskripsi' => 'Nasofaringitis akut (common cold)'],
            ['kode' => 'J02', 'deskripsi' => 'Faringitis akut'],
            ['kode' => 'J03', 'deskripsi' => 'Tonsilitis akut'],
            ['kode' => 'J06', 'deskripsi' => 'Infeksi saluran napas atas akut lainnya'],
            // Penyakit saluran napas bawah
            ['kode' => 'J18', 'deskripsi' => 'Pneumonia, tidak spesifik'],
            ['kode' => 'J20', 'deskripsi' => 'Bronkitis akut'],
            ['kode' => 'J45', 'deskripsi' => 'Asma'],
            // Penyakit kardiovaskular
            ['kode' => 'I10', 'deskripsi' => 'Hipertensi esensial (primer)'],
            ['kode' => 'I50', 'deskripsi' => 'Gagal jantung'],
            // Penyakit metabolik
            ['kode' => 'E11', 'deskripsi' => 'Diabetes melitus tipe 2'],
            ['kode' => 'E78', 'deskripsi' => 'Gangguan metabolisme lipoprotein'],
            // Penyakit muskuloskeletal
            ['kode' => 'M54', 'deskripsi' => 'Dorsalgia (nyeri punggung)'],
            ['kode' => 'M79', 'deskripsi' => 'Gangguan jaringan lunak lainnya'],
            // Penyakit kulit
            ['kode' => 'L30', 'deskripsi' => 'Dermatitis lainnya'],
            // Cedera
            ['kode' => 'S00', 'deskripsi' => 'Cedera superfisial kepala'],
            // Gejala umum
            ['kode' => 'R50', 'deskripsi' => 'Demam tidak diketahui penyebabnya'],
            ['kode' => 'R51', 'deskripsi' => 'Sakit kepala'],
        ];

        foreach ($codes as $code) {
            Icd10Code::updateOrCreate(['kode' => $code['kode']], $code);
        }
    }
}
