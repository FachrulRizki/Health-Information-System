<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'nama'       => 'PT Kimia Farma',
                'alamat'     => 'Jl. Veteran No. 9, Jakarta Pusat',
                'no_telepon' => '02134567890',
                'email'      => 'info@kimiafarma.co.id',
                'is_active'  => true,
            ],
            [
                'nama'       => 'PT Kalbe Farma',
                'alamat'     => 'Jl. Let. Jend. Suprapto Kav. 4, Jakarta Pusat',
                'no_telepon' => '02143567890',
                'email'      => 'info@kalbe.co.id',
                'is_active'  => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(['nama' => $supplier['nama']], $supplier);
        }
    }
}
