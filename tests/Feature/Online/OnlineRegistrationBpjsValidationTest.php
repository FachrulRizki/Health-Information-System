<?php

namespace Tests\Feature\Online;

use App\Models\DoctorSchedule;
use App\Models\Poli;
use App\Models\Doctor;
use App\Models\User;
use App\Services\Integration\BPJSService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests for task 19.3: BPJS validation integration in online registration.
 * Validates Requirement 18.5: BPJS_Service SHALL validate peserta before confirming registration.
 */
class OnlineRegistrationBpjsValidationTest extends TestCase
{
    use RefreshDatabase;

    private array $basePayload;

    protected function setUp(): void
    {
        parent::setUp();

        // Create system user (id=1) used by online registration controller
        User::create([
            'username'           => 'admin',
            'password'           => Hash::make('Password123!'),
            'role'               => 'admin',
            'is_active'          => true,
            'failed_login_count' => 0,
        ]);

        // Create minimal master data
        $poli = Poli::create(['kode_poli' => 'POL01', 'nama_poli' => 'Umum', 'is_active' => true]);

        $this->basePayload = [
            'nama_lengkap'      => 'Pasien Test',
            'tanggal_lahir'     => '1990-01-01',
            'nik'               => '3201010101900001',
            'no_telepon'        => '08123456789',
            'jenis_penjamin'    => 'bpjs',
            'no_bpjs'           => '0000000000001',
            'poli_id'           => $poli->id,
            'tanggal_kunjungan' => now()->addDay()->toDateString(),
        ];

        // Create a doctor schedule so slot check passes
        $doctor = Doctor::create([
            'kode_dokter'  => 'DOK01',
            'nama_dokter'  => 'Dr. Test',
            'is_active'    => true,
        ]);

        DoctorSchedule::create([
            'poli_id'     => $poli->id,
            'doctor_id'   => $doctor->id,
            'hari'        => $this->getDayName(now()->addDay()->dayOfWeek),
            'jam_mulai'   => '08:00',
            'jam_selesai' => '12:00',
            'kuota'       => 20,
            'is_active'   => true,
        ]);
    }

    private function getDayName(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'minggu', 1 => 'senin', 2 => 'selasa', 3 => 'rabu',
            4 => 'kamis', 5 => 'jumat', 6 => 'sabtu',
        };
    }

    /**
     * When BPJS peserta is active, registration proceeds successfully.
     * Requirement 18.5
     */
    public function test_bpjs_registration_succeeds_when_peserta_is_active(): void
    {
        $mockBpjs = $this->createMock(BPJSService::class);
        $mockBpjs->method('validatePeserta')->willReturn([
            'metaData' => ['code' => '200', 'message' => 'OK'],
            'response' => [
                'peserta' => [
                    'noKartu'       => '0000000000001',
                    'nama'          => 'PASIEN TEST',
                    'statusPeserta' => ['kode' => '1', 'keterangan' => 'AKTIF'],
                ],
            ],
        ]);
        $this->app->instance(BPJSService::class, $mockBpjs);

        $response = $this->post(route('online.store'), $this->basePayload);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    /**
     * When BPJS peserta is inactive, registration is rejected with an error.
     * Requirement 18.5
     */
    public function test_bpjs_registration_rejected_when_peserta_is_inactive(): void
    {
        $mockBpjs = $this->createMock(BPJSService::class);
        $mockBpjs->method('validatePeserta')->willReturn([
            'metaData' => ['code' => '200', 'message' => 'OK'],
            'response' => [
                'peserta' => [
                    'noKartu'       => '0000000000001',
                    'nama'          => 'PASIEN TEST',
                    'statusPeserta' => ['kode' => '0', 'keterangan' => 'TIDAK AKTIF'],
                ],
            ],
        ]);
        $this->app->instance(BPJSService::class, $mockBpjs);

        $response = $this->post(route('online.store'), $this->basePayload);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['no_bpjs']);
    }

    /**
     * When BPJS API throws an exception, registration is rejected with an error.
     * Requirement 18.5
     */
    public function test_bpjs_registration_rejected_when_api_throws_exception(): void
    {
        $mockBpjs = $this->createMock(BPJSService::class);
        $mockBpjs->method('validatePeserta')->willThrowException(new \Exception('Connection timeout'));
        $this->app->instance(BPJSService::class, $mockBpjs);

        $response = $this->post(route('online.store'), $this->basePayload);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['no_bpjs']);
    }

    /**
     * When penjamin is umum (not BPJS), BPJS validation is NOT called.
     * Requirement 18.5 (only applies to BPJS penjamin)
     */
    public function test_bpjs_validation_not_called_for_umum_penjamin(): void
    {
        $mockBpjs = $this->createMock(BPJSService::class);
        $mockBpjs->expects($this->never())->method('validatePeserta');
        $this->app->instance(BPJSService::class, $mockBpjs);

        $payload = array_merge($this->basePayload, [
            'jenis_penjamin' => 'umum',
            'no_bpjs'        => null,
        ]);

        $response = $this->post(route('online.store'), $payload);

        // Should not fail due to BPJS validation
        $response->assertSessionDoesntHaveErrors(['no_bpjs']);
    }
}
