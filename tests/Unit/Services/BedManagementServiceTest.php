<?php

namespace Tests\Unit\Services;

use App\Models\Bed;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Room;
use App\Models\User;
use App\Models\Visit;
use App\Services\BedManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BedManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    private BedManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BedManagementService();
        Event::fake();
        Queue::fake();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createUser(): User
    {
        return User::create([
            'username'           => 'user_' . uniqid(),
            'password'           => Hash::make('Password123!'),
            'role'               => 'perawat',  // valid role
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

    private function createRoom(bool $isActive = true): Room
    {
        return Room::create([
            'kode_kamar' => 'KMR-' . uniqid(),
            'nama_kamar' => 'Kamar Test',
            'kelas'      => '1',
            'kapasitas'  => 4,
            'is_active'  => $isActive,
        ]);
    }

    private function createBed(Room $room, string $status = 'tersedia'): Bed
    {
        return Bed::create([
            'room_id'  => $room->id,
            'kode_bed' => 'BED-' . uniqid(),
            'status'   => $status,
        ]);
    }

    // -------------------------------------------------------------------------
    // 1. assignBed() updates bed status to 'terisi'
    // -------------------------------------------------------------------------

    public function test_assign_bed_updates_bed_status_to_terisi(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $room    = $this->createRoom();
        $bed     = $this->createBed($room);

        $this->service->assignBed($visit->id, $bed->id);

        $bed->refresh();
        $this->assertEquals('terisi', $bed->status);
    }

    public function test_assign_bed_sets_current_patient_id(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $room    = $this->createRoom();
        $bed     = $this->createBed($room);

        $this->service->assignBed($visit->id, $bed->id);

        $bed->refresh();
        $this->assertEquals($patient->id, $bed->current_patient_id);
    }

    public function test_assign_bed_creates_inpatient_record(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $room    = $this->createRoom();
        $bed     = $this->createBed($room);

        $record = $this->service->assignBed($visit->id, $bed->id);

        $this->assertNotNull($record);
        $this->assertEquals($visit->id, $record->visit_id);
        $this->assertEquals($bed->id, $record->bed_id);
        $this->assertEquals('dirawat', $record->status_pulang);
    }

    // -------------------------------------------------------------------------
    // 2. releaseBed() updates bed status to 'tersedia'
    // -------------------------------------------------------------------------

    public function test_release_bed_updates_bed_status_to_tersedia(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $room    = $this->createRoom();
        $bed     = $this->createBed($room);

        // First assign the bed
        $this->service->assignBed($visit->id, $bed->id);

        // Then release it
        $this->service->releaseBed($bed->id);

        $bed->refresh();
        $this->assertEquals('tersedia', $bed->status);
    }

    public function test_release_bed_clears_current_patient_id(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $room    = $this->createRoom();
        $bed     = $this->createBed($room);

        $this->service->assignBed($visit->id, $bed->id);
        $this->service->releaseBed($bed->id);

        $bed->refresh();
        $this->assertNull($bed->current_patient_id);
    }

    // -------------------------------------------------------------------------
    // 3. getBedMap() returns rooms with beds
    // -------------------------------------------------------------------------

    public function test_get_bed_map_returns_active_rooms_with_beds(): void
    {
        $room = $this->createRoom(true);
        $this->createBed($room);
        $this->createBed($room);

        $bedMap = $this->service->getBedMap();

        $this->assertCount(1, $bedMap);
        $this->assertEquals($room->id, $bedMap->first()->id);
        $this->assertCount(2, $bedMap->first()->beds);
    }

    public function test_get_bed_map_does_not_return_inactive_rooms(): void
    {
        $activeRoom   = $this->createRoom(true);
        $inactiveRoom = $this->createRoom(false);
        $this->createBed($activeRoom);
        $this->createBed($inactiveRoom);

        $bedMap = $this->service->getBedMap();

        $this->assertCount(1, $bedMap);
        $this->assertEquals($activeRoom->id, $bedMap->first()->id);
    }

    public function test_get_bed_map_returns_empty_when_no_active_rooms(): void
    {
        $this->createRoom(false);

        $bedMap = $this->service->getBedMap();

        $this->assertCount(0, $bedMap);
    }
}
