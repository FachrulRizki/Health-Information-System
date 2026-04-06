<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $dokterUser = User::where('username', 'dokter')->first();
        $spUmum     = Specialization::where('kode', 'SP-UMM')->first();
        $spGigi     = Specialization::where('kode', 'SP-GIG')->first();
        $spAnak     = Specialization::where('kode', 'SP-ANK')->first();

        $doctors = [
            [
                'kode_dokter'         => 'DR-001',
                'nama_dokter'         => 'dr. Budi Santoso',
                'user_id'             => $dokterUser?->id,
                'specialization_id'   => $spUmum?->id,
                'sub_specialization_id' => null,
                'no_sip'              => 'SIP/001/2024',
                'no_telepon'          => '081234567890',
                'is_active'           => true,
            ],
            [
                'kode_dokter'         => 'DR-002',
                'nama_dokter'         => 'drg. Siti Rahayu',
                'user_id'             => null,
                'specialization_id'   => $spGigi?->id,
                'sub_specialization_id' => null,
                'no_sip'              => 'SIP/002/2024',
                'no_telepon'          => '081234567891',
                'is_active'           => true,
            ],
            [
                'kode_dokter'         => 'DR-003',
                'nama_dokter'         => 'dr. Ahmad Fauzi, Sp.A',
                'user_id'             => null,
                'specialization_id'   => $spAnak?->id,
                'sub_specialization_id' => null,
                'no_sip'              => 'SIP/003/2024',
                'no_telepon'          => '081234567892',
                'is_active'           => true,
            ],
        ];

        foreach ($doctors as $doctor) {
            Doctor::updateOrCreate(['kode_dokter' => $doctor['kode_dokter']], $doctor);
        }
    }
}
