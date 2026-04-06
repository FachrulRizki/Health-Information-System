<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PoliSeeder::class,
            SpecializationSeeder::class,
            DoctorSeeder::class,
            Icd10Seeder::class,
            Icd9cmSeeder::class,
            DrugCategorySeeder::class,
            DrugUnitSeeder::class,
            SupplierSeeder::class,
            DrugSeeder::class,
            RoomSeeder::class,
            BedSeeder::class,
            ExaminationTypeSeeder::class,
            ApiSettingsSeeder::class,
        ]);
    }
}
