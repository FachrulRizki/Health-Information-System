<?php

namespace Database\Seeders;

use App\Models\DrugUnit;
use Illuminate\Database\Seeder;

class DrugUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = ['Tablet', 'Kapsul', 'Botol', 'Ampul', 'Sachet'];

        foreach ($units as $nama) {
            DrugUnit::updateOrCreate(['nama' => $nama], ['nama' => $nama]);
        }
    }
}
