<?php

namespace Tests\Unit\Services;

use App\Models\Diagnosis;
use App\Models\Icd10Code;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\User;
use App\Models\Visit;
use App\Services\RMEService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RMEServiceTest extends TestCase
{
    use RefreshDatabase;

    private RMEService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RMEService();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createUser(): User
    {
        return User::create([
            'username'           => 'user_' . uniqid(),
            'password'           => Hash::make('Password123!'),
            'role'               => 'dokter',  // valid role
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

    private function createVisit(Patient $patient, Poli $poli, User $user, array $overrides = []): Visit
    {
        return Visit::create(array_merge([
            'no_rawat'          => 'RWT-' . date('Ymd') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'patient_id'        => $patient->id,
            'poli_id'           => $poli->id,
            'user_id'           => $user->id,
            'jenis_penjamin'    => 'umum',
            'status'            => 'dalam_pemeriksaan',
            'tanggal_kunjungan' => today()->toDateString(),
        ], $overrides));
    }

    private function createIcd10(string $kode = 'A00', string $deskripsi = 'Cholera'): Icd10Code
    {
        return Icd10Code::create(['kode' => $kode, 'deskripsi' => $deskripsi]);
    }

    // -------------------------------------------------------------------------
    // 1. saveSOAP() throws ValidationException when no diagnoses
    // -------------------------------------------------------------------------

    public function test_save_soap_throws_validation_exception_when_no_diagnoses(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);

        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        $this->service->saveSOAP($visit->id, [
            'subjective' => 'Pasien mengeluh demam',
            'objective'  => 'Suhu 38.5°C',
            'assessment' => 'Demam',
            'plan'       => 'Istirahat',
            'diagnoses'  => [],
        ]);
    }

    public function test_save_soap_throws_validation_exception_when_diagnoses_missing(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);

        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        $this->service->saveSOAP($visit->id, [
            'subjective' => 'Pasien mengeluh demam',
        ]);
    }

    // -------------------------------------------------------------------------
    // 2. saveSOAP() saves SOAP data with diagnoses
    // -------------------------------------------------------------------------

    public function test_save_soap_saves_data_with_diagnoses(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $icd10   = $this->createIcd10('A00', 'Cholera');

        $this->actingAs($user);

        $record = $this->service->saveSOAP($visit->id, [
            'subjective' => 'Pasien mengeluh demam',
            'objective'  => 'Suhu 38.5°C',
            'assessment' => 'Demam',
            'plan'       => 'Istirahat dan minum obat',
            'diagnoses'  => ['A00'],
        ]);

        $this->assertNotNull($record);
        $this->assertEquals($visit->id, $record->visit_id);
        $this->assertEquals('Pasien mengeluh demam', $record->subjective);

        $diagnoses = Diagnosis::where('visit_id', $visit->id)->get();
        $this->assertCount(1, $diagnoses);
        $this->assertEquals('A00', $diagnoses->first()->icd10_code);
        $this->assertTrue((bool) $diagnoses->first()->is_primary);
    }

    public function test_save_soap_marks_first_diagnosis_as_primary(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user);
        $this->createIcd10('A00', 'Cholera');
        $this->createIcd10('B01', 'Varicella');

        $this->actingAs($user);

        $this->service->saveSOAP($visit->id, [
            'subjective' => 'Keluhan',
            'diagnoses'  => ['A00', 'B01'],
        ]);

        $diagnoses = Diagnosis::where('visit_id', $visit->id)->orderBy('id')->get();
        $this->assertTrue((bool) $diagnoses->first()->is_primary);
        $this->assertFalse((bool) $diagnoses->last()->is_primary);
    }

    // -------------------------------------------------------------------------
    // 3. searchIcd10() returns matching codes
    // -------------------------------------------------------------------------

    public function test_search_icd10_returns_matching_by_kode(): void
    {
        $this->createIcd10('A00', 'Cholera');
        $this->createIcd10('B01', 'Varicella');

        $results = $this->service->searchIcd10('A00');

        $this->assertCount(1, $results);
        $this->assertEquals('A00', $results->first()->kode);
    }

    public function test_search_icd10_returns_matching_by_deskripsi(): void
    {
        $this->createIcd10('A00', 'Cholera');
        $this->createIcd10('B01', 'Varicella');

        $results = $this->service->searchIcd10('Varicella');

        $this->assertCount(1, $results);
        $this->assertEquals('B01', $results->first()->kode);
    }

    public function test_search_icd10_returns_empty_when_no_match(): void
    {
        $this->createIcd10('A00', 'Cholera');

        $results = $this->service->searchIcd10('XYZ999');

        $this->assertCount(0, $results);
    }

    // -------------------------------------------------------------------------
    // 4. validateSKDP() throws when no_sep is missing
    // -------------------------------------------------------------------------

    public function test_validate_skdp_throws_when_no_sep_is_missing(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user, ['no_sep' => null]);

        $this->expectException(ValidationException::class);

        $this->service->validateSKDP($visit->id, [
            'tanggal_rencana_kontrol' => now()->addDays(7)->toDateString(),
            'specialization_id'       => 1,
            'dpjp_doctor_id'          => 1,
        ]);
    }

    public function test_validate_skdp_throws_when_tanggal_rencana_kontrol_is_missing(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user, ['no_sep' => 'SEP-001']);

        $this->expectException(ValidationException::class);

        $this->service->validateSKDP($visit->id, [
            'specialization_id' => 1,
            'dpjp_doctor_id'    => 1,
        ]);
    }

    public function test_validate_skdp_throws_when_tanggal_rencana_kontrol_is_past(): void
    {
        $user    = $this->createUser();
        $poli    = $this->createPoli();
        $patient = $this->createPatient();
        $visit   = $this->createVisit($patient, $poli, $user, ['no_sep' => 'SEP-001']);

        $this->expectException(ValidationException::class);

        $this->service->validateSKDP($visit->id, [
            'tanggal_rencana_kontrol' => now()->subDays(1)->toDateString(),
            'specialization_id'       => 1,
            'dpjp_doctor_id'          => 1,
        ]);
    }
}
