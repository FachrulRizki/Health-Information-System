<?php

namespace Tests\Unit\Services;

use App\Models\Drug;
use App\Models\DrugCategory;
use App\Models\DrugStock;
use App\Models\DrugUnit;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Models\Visit;
use App\Services\PharmacyService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class PharmacyServiceTest extends TestCase
{
    use RefreshDatabase;

    private PharmacyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PharmacyService();
    }

    private function createDrug(): Drug
    {
        $category = DrugCategory::create(['nama' => 'Kategori '.uniqid()]);
        $unit     = DrugUnit::create(['nama' => 'Tablet']);
        return Drug::create(['kode' => 'DRG-'.uniqid(), 'nama' => 'Paracetamol', 'drug_category_id' => $category->id, 'drug_unit_id' => $unit->id, 'harga_beli' => 1000, 'harga_jual' => 2000, 'is_active' => true]);
    }

    private function createPrescriptionWithItem(Drug $drug, float $quantity): array
    {
        $user    = User::create(['username' => 'user_'.uniqid(), 'password' => Hash::make('P@ss1234'), 'role' => 'farmasi', 'is_active' => true, 'failed_login_count' => 0]);
        $poli    = Poli::create(['kode_poli' => 'PLI-'.uniqid(), 'nama_poli' => 'Poli Umum', 'is_active' => true]);
        $patient = Patient::create(['no_rm' => 'RM-'.date('Ymd').'-'.str_pad(rand(1,99999),5,'0',STR_PAD_LEFT), 'nama_lengkap' => 'Pasien Test', 'tanggal_lahir' => '1990-01-01', 'jenis_kelamin' => 'L', 'jenis_penjamin' => 'umum']);
        $visit   = Visit::create(['no_rawat' => 'RWT-'.date('Ymd').'-'.str_pad(rand(1,99999),5,'0',STR_PAD_LEFT), 'patient_id' => $patient->id, 'poli_id' => $poli->id, 'user_id' => $user->id, 'jenis_penjamin' => 'umum', 'status' => 'farmasi', 'tanggal_kunjungan' => today()->toDateString()]);
        $prescription = Prescription::create(['visit_id' => $visit->id, 'type' => 'dokter', 'status' => 'pending', 'prescribed_by' => $user->id]);
        $item = PrescriptionItem::create(['prescription_id' => $prescription->id, 'drug_id' => $drug->id, 'quantity' => $quantity, 'dosage' => '3x1', 'instructions' => 'Sesudah makan']);
        return [$prescription, $item];
    }

    public function test_validate_prescription_returns_valid_true_when_stock_sufficient(): void
    {
        $drug = $this->createDrug();
        DrugStock::create(['drug_id' => $drug->id, 'quantity' => 100, 'expiry_date' => Carbon::today()->addYear()->toDateString(), 'batch_number' => 'B001', 'minimum_stock' => 10]);
        [$prescription] = $this->createPrescriptionWithItem($drug, 10);
        $result = $this->service->validatePrescription($prescription->id);
        $this->assertTrue($result['valid']);
        $this->assertTrue($result['items'][0]['is_sufficient']);
    }

    public function test_validate_prescription_returns_valid_false_when_stock_insufficient(): void
    {
        $drug = $this->createDrug();
        DrugStock::create(['drug_id' => $drug->id, 'quantity' => 5, 'expiry_date' => Carbon::today()->addYear()->toDateString(), 'batch_number' => 'B001', 'minimum_stock' => 10]);
        [$prescription] = $this->createPrescriptionWithItem($drug, 20);
        $result = $this->service->validatePrescription($prescription->id);
        $this->assertFalse($result['valid']);
        $this->assertFalse($result['items'][0]['is_sufficient']);
    }

    public function test_dispense_drug_reduces_stock_by_exact_quantity(): void
    {
        $drug = $this->createDrug();
        DrugStock::create(['drug_id' => $drug->id, 'quantity' => 50, 'expiry_date' => Carbon::today()->addYear()->toDateString(), 'batch_number' => 'B001', 'minimum_stock' => 5]);
        [, $item] = $this->createPrescriptionWithItem($drug, 10);
        $this->service->dispenseDrug($item->id, 10);
        $this->assertEquals(40, (float) DrugStock::where('drug_id', $drug->id)->sum('quantity'));
    }

    public function test_dispense_drug_throws_exception_for_expired_drug(): void
    {
        $drug = $this->createDrug();
        DrugStock::create(['drug_id' => $drug->id, 'quantity' => 50, 'expiry_date' => Carbon::today()->subDay()->toDateString(), 'batch_number' => 'B-EXP', 'minimum_stock' => 5]);
        [, $item] = $this->createPrescriptionWithItem($drug, 10);
        $this->expectException(RuntimeException::class);
        $this->service->dispenseDrug($item->id, 10);
    }

    public function test_check_expiry_returns_expired_true_for_past_date(): void
    {
        $drug  = $this->createDrug();
        $stock = DrugStock::create(['drug_id' => $drug->id, 'quantity' => 10, 'expiry_date' => Carbon::today()->subMonth()->toDateString(), 'batch_number' => 'B-OLD', 'minimum_stock' => 5]);
        $result = $this->service->checkExpiry($stock->id);
        $this->assertTrue($result['expired']);
    }

    public function test_check_expiry_returns_expired_false_for_future_date(): void
    {
        $drug  = $this->createDrug();
        $stock = DrugStock::create(['drug_id' => $drug->id, 'quantity' => 10, 'expiry_date' => Carbon::today()->addYear()->toDateString(), 'batch_number' => 'B-GOOD', 'minimum_stock' => 5]);
        $result = $this->service->checkExpiry($stock->id);
        $this->assertFalse($result['expired']);
    }

    public function test_get_stock_alerts_returns_low_stock_alert(): void
    {
        $drug = $this->createDrug();
        DrugStock::create(['drug_id' => $drug->id, 'quantity' => 3, 'expiry_date' => Carbon::today()->addYear()->toDateString(), 'batch_number' => 'B-LOW', 'minimum_stock' => 10]);
        $alerts = $this->service->getStockAlerts();
        $lowStock = array_filter($alerts, fn($a) => $a['alert_type'] === 'low_stock');
        $this->assertNotEmpty($lowStock);
    }

    public function test_get_stock_alerts_returns_expired_alert(): void
    {
        $drug = $this->createDrug();
        DrugStock::create(['drug_id' => $drug->id, 'quantity' => 20, 'expiry_date' => Carbon::today()->subDay()->toDateString(), 'batch_number' => 'B-EXP', 'minimum_stock' => 5]);
        $alerts = $this->service->getStockAlerts();
        $expired = array_filter($alerts, fn($a) => $a['alert_type'] === 'expired');
        $this->assertNotEmpty($expired);
    }
}
