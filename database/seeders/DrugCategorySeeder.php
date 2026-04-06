<?php

namespace Database\Seeders;

use App\Models\DrugCategory;
use Illuminate\Database\Seeder;

class DrugCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Antibiotik', 'Analgesik', 'Antihipertensi', 'Vitamin', 'Antidiabetik'];

        foreach ($categories as $nama) {
            DrugCategory::updateOrCreate(['nama' => $nama], ['nama' => $nama]);
        }
    }
}
