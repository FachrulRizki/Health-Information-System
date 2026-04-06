<?php

namespace Tests\Unit\Services;

use App\Models\Patient;
use App\Models\Poli;
use App\Models\QueueEntry;
use App\Models\User;
use App\Models\Visit;
use App\Services\QueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QueueServiceTest extends TestCase
{
    use RefreshDatabase;

    private QueueService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QueueService();
        Event::fake();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createUser(): User
    {
        return User::create([
            'username'           => 'user_' . uniqid(),
            'password'           => Hash::make('Password123!'),
            'role'               => 'petugas_pendaftaran',
            'is_active'          => true,
            'failed_login_count' => 0,
        ]);
    }

    private function createPoli(): Poli
    {
        return Poli::create([
            'kode_poli' => 'PLI-' . uniqid(),
            'nama_poli' => 'Poli Umum',
            'is_active' => true,
        ]);
    }

    private function createPatient(): Patient
    {
        return Patient::create([
            'no_rm'          => 'RM-' . date('Ymd') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'nama_lengkap'   => 'Pasien Test',
            'tanggal_lahir'  => '1990-01-01',
            'jenis_kelamin'  => 'L',
            'jenis_penjamin' => 'umum',
        ]);
    }

    private function createVisit(Patient $patient, Poli $poli, User $user): Visit
    {
        return Visit::create([
            'no_rawat'          => 'RWT-' . date('Ymd') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'patient_id'        => $patient->id,
            'poli_id'           => $poli->id,
            'user_id'           => $user->id,
            'jenis_penjamin'    => 'umum',
            'status'            => 'pendaftaran',
            'tanggal_kunjungan' => today()->toDateString(),
        ]);
    }

    // -------------------------------------------------------------------------
    // 1. assignQueue() creates queue entry with status 'menunggu'
    // -------------------------------------------------------------------------

    public function test_assign_queue_creates_entry_with_status_menunggu(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);

        $entry = $this->service->assignQueue($visit->id, $poli->id);

        $this->assertInstanceOf(QueueEntry::class, $entry);
        $this->assertEquals('menunggu', $entry->status);
        $this->assertEquals($visit->id, $entry->visit_id);
        $this->assertEquals($poli->id, $entry->poli_id);
        $this->assertEquals(1, $entry->queue_number);
    }

    public function test_assign_queue_increments_queue_number(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();

        $visit1 = $this->createVisit($patient, $poli, $user);
        $entry1 = $this->service->assignQueue($visit1->id, $poli->id);

        $patient2 = $this->createPatient();
        $visit2   = $this->createVisit($patient2, $poli, $user);
        $entry2   = $this->service->assignQueue($visit2->id, $poli->id);

        $this->assertEquals(1, $entry1->queue_number);
        $this->assertEquals(2, $entry2->queue_number);
    }

    // -------------------------------------------------------------------------
    // 2. updateStatus() updates queue entry status
    // -------------------------------------------------------------------------

    public function test_update_status_changes_queue_entry_status(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $entry   = $this->service->assignQueue($visit->id, $poli->id);

        $updated = $this->service->updateStatus($entry->id, 'dipanggil');

        $this->assertEquals('dipanggil', $updated->status);
    }

    public function test_update_status_to_selesai(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $entry   = $this->service->assignQueue($visit->id, $poli->id);

        $updated = $this->service->updateStatus($entry->id, 'selesai');

        $this->assertEquals('selesai', $updated->status);

        // Visit status should also be updated
        $visit->refresh();
        $this->assertEquals('selesai', $visit->status);
    }

    // -------------------------------------------------------------------------
    // 3. getQueueByPoli() returns queue entries for a poli
    // -------------------------------------------------------------------------

    public function test_get_queue_by_poli_returns_entries_for_poli(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $this->service->assignQueue($visit->id, $poli->id);

        $queue = $this->service->getQueueByPoli($poli->id);

        $this->assertCount(1, $queue);
        $this->assertEquals($poli->id, $queue->first()->poli_id);
    }

    public function test_get_queue_by_poli_does_not_return_other_poli_entries(): void
    {
        $user     = $this->createUser();
        $poli1    = $this->createPoli();
        $poli2    = $this->createPoli();
        $patient  = $this->createPatient();
        $visit    = $this->createVisit($patient, $poli1, $user);
        $this->service->assignQueue($visit->id, $poli1->id);

        $queue = $this->service->getQueueByPoli($poli2->id);

        $this->assertCount(0, $queue);
    }

    public function test_get_queue_by_poli_returns_entries_ordered_by_queue_number(): void
    {
        $user     = $this->createUser();
        $poli     = $this->createPoli();

        $patient1 = $this->createPatient();
        $visit1   = $this->createVisit($patient1, $poli, $user);
        $this->service->assignQueue($visit1->id, $poli->id);

        $patient2 = $this->createPatient();
        $visit2   = $this->createVisit($patient2, $poli, $user);
        $this->service->assignQueue($visit2->id, $poli->id);

        $queue = $this->service->getQueueByPoli($poli->id);

        $this->assertEquals(1, $queue->first()->queue_number);
        $this->assertEquals(2, $queue->last()->queue_number);
    }
}
