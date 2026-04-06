<?php

namespace Tests\Unit\Services;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\User;
use App\Models\Visit;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BillingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BillingService();
        Queue::fake();
    }

    private function createVisit(): Visit
    {
        $user = User::create(['username' => 'user_'.uniqid(), 'password' => Hash::make('P@ss1234'), 'role' => 'kasir', 'is_active' => true, 'failed_login_count' => 0]);
        $poli = Poli::create(['kode_poli' => 'PLI-'.uniqid(), 'nama_poli' => 'Poli Umum', 'is_active' => true]);
        $patient = Patient::create(['no_rm' => 'RM-'.date('Ymd').'-'.str_pad(rand(1,99999),5,'0',STR_PAD_LEFT), 'nama_lengkap' => 'Pasien Test', 'tanggal_lahir' => '1990-01-01', 'jenis_kelamin' => 'L', 'jenis_penjamin' => 'umum']);
        return Visit::create(['no_rawat' => 'RWT-'.date('Ymd').'-'.str_pad(rand(1,99999),5,'0',STR_PAD_LEFT), 'patient_id' => $patient->id, 'poli_id' => $poli->id, 'user_id' => $user->id, 'jenis_penjamin' => 'umum', 'status' => 'kasir', 'tanggal_kunjungan' => today()->toDateString()]);
    }

    public function test_generate_bill_creates_bill_for_visit(): void
    {
        $visit = $this->createVisit();
        $bill  = $this->service->generateBill($visit->id);
        $this->assertInstanceOf(Bill::class, $bill);
        $this->assertEquals($visit->id, $bill->visit_id);
        $this->assertEquals('pending', $bill->status);
    }

    public function test_generate_bill_is_idempotent(): void
    {
        $visit = $this->createVisit();
        $bill1 = $this->service->generateBill($visit->id);
        $bill2 = $this->service->generateBill($visit->id);
        $this->assertEquals($bill1->id, $bill2->id);
        $this->assertDatabaseCount('bills', 1);
    }

    public function test_add_bill_item_creates_item_and_updates_total(): void
    {
        $visit = $this->createVisit();
        $bill  = $this->service->generateBill($visit->id);
        $item  = $this->service->addBillItem($bill->id, 'konsultasi', 'Biaya Konsultasi', 50000.00, 1);
        $this->assertInstanceOf(BillItem::class, $item);
        $this->assertEquals(50000.00, (float) $item->subtotal);
        $bill->refresh();
        $this->assertEquals(50000.00, (float) $bill->total_amount);
    }

    public function test_add_bill_item_updates_total_with_multiple_items(): void
    {
        $visit = $this->createVisit();
        $bill  = $this->service->generateBill($visit->id);
        $this->service->addBillItem($bill->id, 'konsultasi', 'Konsultasi', 50000.00, 1);
        $this->service->addBillItem($bill->id, 'obat', 'Paracetamol', 10000.00, 3);
        $bill->refresh();
        $this->assertEquals(80000.00, (float) $bill->total_amount);
    }

    public function test_process_payment_updates_status_to_paid(): void
    {
        $visit = $this->createVisit();
        $bill  = $this->service->generateBill($visit->id);
        $paid  = $this->service->processPayment($bill->id, 'umum');
        $this->assertEquals('paid', $paid->status);
        $this->assertEquals('umum', $paid->payment_method);
    }

    public function test_process_payment_bpjs_dispatches_claim_job(): void
    {
        $visit = $this->createVisit();
        $bill  = $this->service->generateBill($visit->id);
        $this->service->processPayment($bill->id, 'bpjs');
        Queue::assertPushed(\App\Jobs\SendBPJSClaimJob::class);
    }

    public function test_total_amount_equals_sum_of_all_bill_items(): void
    {
        $visit = $this->createVisit();
        $bill  = $this->service->generateBill($visit->id);
        $this->service->addBillItem($bill->id, 'konsultasi', 'Konsultasi', 75000.00, 1);
        $this->service->addBillItem($bill->id, 'tindakan', 'Injeksi', 25000.00, 2);
        $this->service->addBillItem($bill->id, 'obat', 'Amoxicillin', 5000.00, 10);
        $bill->refresh();
        $expected = 75000 + 50000 + 50000;
        $this->assertEquals($expected, (float) $bill->total_amount);
        $dbSum = BillItem::where('bill_id', $bill->id)->sum(DB::raw('unit_price * quantity'));
        $this->assertEquals((float) $dbSum, (float) $bill->total_amount);
    }
}
