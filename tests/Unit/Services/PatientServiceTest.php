<?php

namespace Tests\Unit\Services;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\User;
use App\Models\Visit;
use App\Services\PatientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientServiceTest extends TestCase
{
    use RefreshDatabase;

    private PatientService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PatientService();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createUser(): User
    {
        return User::create([
            'username'           => 'testuser_' . uniqid(),
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

    private function createPatientData(): array
    {
        return [
            'nama_lengkap'   => 'Budi Santoso',
            'tanggal_lahir'  => '1990-01-15',
            'jenis_kelamin'  => 'L',
            'alamat'         => 'Jl. Merdeka No. 1',
            'jenis_penjamin' => 'umum',
            'nik'            => '3201234567890001',
            'no_telepon'     => '081234567890',
        ];
    }

    // -------------------------------------------------------------------------
    // 1. generateNoRM() returns unique NoRM in format RM-YYYYMMDD-XXXXX
    // -------------------------------------------------------------------------

    public function test_generate_no_rm_returns_correct_format(): void
    {
        $noRM = $this->service->generateNoRM();

        $this->assertMatchesRegularExpression('/^RM-\d{8}-\d{5}$/', $noRM);
    }

    public function test_generate_no_rm_returns_unique_values(): void
    {
        $noRM1 = $this->service->generateNoRM();

        // Create a patient with the first NoRM so the next call generates a new one
        Patient::create([
            'no_rm'          => $noRM1,
            'nama_lengkap'   => 'Pasien Satu',
            'tanggal_lahir'  => '1990-01-01',
            'jenis_kelamin'  => 'L',
            'jenis_penjamin' => 'umum',
        ]);

        $noRM2 = $this->service->generateNoRM();

        $this->assertNotEquals($noRM1, $noRM2);
        $this->assertMatchesRegularExpression('/^RM-\d{8}-\d{5}$/', $noRM2);
    }

    // -------------------------------------------------------------------------
    // 2. generateNoRawat() returns unique NoRawat in format RWT-YYYYMMDD-XXXXX
    // -------------------------------------------------------------------------

    public function test_generate_no_rawat_returns_correct_format(): void
    {
        $noRawat = $this->service->generateNoRawat();

        $this->assertMatchesRegularExpression('/^RWT-\d{8}-\d{5}$/', $noRawat);
    }

    public function test_generate_no_rawat_returns_unique_values(): void
    {
        $user  = $this->createUser();
        $poli  = $this->createPoli();

        $noRawat1 = $this->service->generateNoRawat();

        // Create a patient and visit with the first NoRawat
        $patient = Patient::create([
            'no_rm'          => 'RM-' . date('Ymd') . '-00001',
            'nama_lengkap'   => 'Pasien Satu',
            'tanggal_lahir'  => '1990-01-01',
            'jenis_kelamin'  => 'L',
            'jenis_penjamin' => 'umum',
        ]);

        Visit::create([
            'no_rawat'          => $noRawat1,
            'patient_id'        => $patient->id,
            'poli_id'           => $poli->id,
            'user_id'           => $user->id,
            'jenis_penjamin'    => 'umum',
            'status'            => 'pendaftaran',
            'tanggal_kunjungan' => today()->toDateString(),
        ]);

        $noRawat2 = $this->service->generateNoRawat();

        $this->assertNotEquals($noRawat1, $noRawat2);
        $this->assertMatchesRegularExpression('/^RWT-\d{8}-\d{5}$/', $noRawat2);
    }

    // -------------------------------------------------------------------------
    // 3. createPatient() encrypts NIK and phone
    // -------------------------------------------------------------------------

    public function test_create_patient_encrypts_nik_and_phone(): void
    {
        $data    = $this->createPatientData();
        $patient = $this->service->createPatient($data);

        // Raw DB values should be encrypted (not plain text)
        $this->assertNotEquals($data['nik'], $patient->nik_encrypted);
        $this->assertNotEquals($data['no_telepon'], $patient->no_telepon_encrypted);

        // Decrypted values should match originals
        $this->assertEquals($data['nik'], Crypt::decryptString($patient->nik_encrypted));
        $this->assertEquals($data['no_telepon'], Crypt::decryptString($patient->no_telepon_encrypted));
    }

    public function test_create_patient_stores_no_rm(): void
    {
        $data    = $this->createPatientData();
        $patient = $this->service->createPatient($data);

        $this->assertNotNull($patient->no_rm);
        $this->assertMatchesRegularExpression('/^RM-\d{8}-\d{5}$/', $patient->no_rm);
    }

    // -------------------------------------------------------------------------
    // 4. searchPatients() returns matching patients
    // -------------------------------------------------------------------------

    public function test_search_patients_returns_matching_by_name(): void
    {
        Patient::create([
            'no_rm'          => 'RM-20240101-00001',
            'nama_lengkap'   => 'Budi Santoso',
            'tanggal_lahir'  => '1990-01-01',
            'jenis_kelamin'  => 'L',
            'jenis_penjamin' => 'umum',
        ]);

        Patient::create([
            'no_rm'          => 'RM-20240101-00002',
            'nama_lengkap'   => 'Siti Rahayu',
            'tanggal_lahir'  => '1992-05-10',
            'jenis_kelamin'  => 'P',
            'jenis_penjamin' => 'umum',
        ]);

        $results = $this->service->searchPatients('Budi');

        $this->assertCount(1, $results);
        $this->assertEquals('Budi Santoso', $results->first()->nama_lengkap);
    }

    public function test_search_patients_returns_matching_by_no_rm(): void
    {
        Patient::create([
            'no_rm'          => 'RM-20240101-00001',
            'nama_lengkap'   => 'Budi Santoso',
            'tanggal_lahir'  => '1990-01-01',
            'jenis_kelamin'  => 'L',
            'jenis_penjamin' => 'umum',
        ]);

        $results = $this->service->searchPatients('RM-20240101-00001');

        $this->assertCount(1, $results);
        $this->assertEquals('RM-20240101-00001', $results->first()->no_rm);
    }

    public function test_search_patients_returns_empty_when_no_match(): void
    {
        $results = $this->service->searchPatients('TidakAda');

        $this->assertCount(0, $results);
    }
}
