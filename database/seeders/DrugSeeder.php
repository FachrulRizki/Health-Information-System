<?php

namespace Database\Seeders;

use App\Models\Drug;
use App\Models\DrugCategory;
use App\Models\DrugUnit;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DrugSeeder extends Seeder
{
    public function run(): void
    {
        $antibiotik    = DrugCategory::where('nama', 'Antibiotik')->first();
        $analgesik     = DrugCategory::where('nama', 'Analgesik')->first();
        $antihipertensi = DrugCategory::where('nama', 'Antihipertensi')->first();
        $vitamin       = DrugCategory::where('nama', 'Vitamin')->first();
        $antidiabetik  = DrugCategory::where('nama', 'Antidiabetik')->first();

        $tablet  = DrugUnit::where('nama', 'Tablet')->first();
        $kapsul  = DrugUnit::where('nama', 'Kapsul')->first();
        $botol   = DrugUnit::where('nama', 'Botol')->first();
        $ampul   = DrugUnit::where('nama', 'Ampul')->first();

        $kimiaFarma = Supplier::where('nama', 'PT Kimia Farma')->first();
        $kalbe      = Supplier::where('nama', 'PT Kalbe Farma')->first();

        $drugs = [
            [
                'kode' => 'OBT-001', 'nama' => 'Amoxicillin 500mg',
                'drug_category_id' => $antibiotik?->id, 'drug_unit_id' => $kapsul?->id,
                'supplier_id' => $kimiaFarma?->id, 'harga_beli' => 1500, 'harga_jual' => 2500, 'is_active' => true,
            ],
            [
                'kode' => 'OBT-002', 'nama' => 'Ciprofloxacin 500mg',
                'drug_category_id' => $antibiotik?->id, 'drug_unit_id' => $tablet?->id,
                'supplier_id' => $kimiaFarma?->id, 'harga_beli' => 2000, 'harga_jual' => 3500, 'is_active' => true,
            ],
            [
                'kode' => 'OBT-003', 'nama' => 'Paracetamol 500mg',
                'drug_category_id' => $analgesik?->id, 'drug_unit_id' => $tablet?->id,
                'supplier_id' => $kalbe?->id, 'harga_beli' => 500, 'harga_jual' => 1000, 'is_active' => true,
            ],
            [
                'kode' => 'OBT-004', 'nama' => 'Ibuprofen 400mg',
                'drug_category_id' => $analgesik?->id, 'drug_unit_id' => $tablet?->id,
                'supplier_id' => $kalbe?->id, 'harga_beli' => 800, 'harga_jual' => 1500, 'is_active' => true,
            ],
            [
                'kode' => 'OBT-005', 'nama' => 'Amlodipine 5mg',
                'drug_category_id' => $antihipertensi?->id, 'drug_unit_id' => $tablet?->id,
                'supplier_id' => $kimiaFarma?->id, 'harga_beli' => 1200, 'harga_jual' => 2000, 'is_active' => true,
            ],
            [
                'kode' => 'OBT-006', 'nama' => 'Captopril 25mg',
                'drug_category_id' => $antihipertensi?->id, 'drug_unit_id' => $tablet?->id,
                'supplier_id' => $kimiaFarma?->id, 'harga_beli' => 700, 'harga_jual' => 1200, 'is_active' => true,
            ],
            [
                'kode' => 'OBT-007', 'nama' => 'Vitamin C 500mg',
                'drug_category_id' => $vitamin?->id, 'drug_unit_id' => $tablet?->id,
                'supplier_id' => $kalbe?->id, 'harga_beli' => 300, 'harga_jual' => 600, 'is_active' => true,
            ],
            [
                'kode' => 'OBT-008', 'nama' => 'Vitamin B Complex',
                'drug_category_id' => $vitamin?->id, 'drug_unit_id' => $tablet?->id,
                'supplier_id' => $kalbe?->id, 'harga_beli' => 400, 'harga_jual' => 800, 'is_active' => true,
            ],
            [
                'kode' => 'OBT-009', 'nama' => 'Metformin 500mg',
                'drug_category_id' => $antidiabetik?->id, 'drug_unit_id' => $tablet?->id,
                'supplier_id' => $kimiaFarma?->id, 'harga_beli' => 900, 'harga_jual' => 1500, 'is_active' => true,
            ],
            [
                'kode' => 'OBT-010', 'nama' => 'Glibenclamide 5mg',
                'drug_category_id' => $antidiabetik?->id, 'drug_unit_id' => $tablet?->id,
                'supplier_id' => $kimiaFarma?->id, 'harga_beli' => 600, 'harga_jual' => 1000, 'is_active' => true,
            ],
        ];

        foreach ($drugs as $drug) {
            Drug::updateOrCreate(['kode' => $drug['kode']], $drug);
        }
    }
}
