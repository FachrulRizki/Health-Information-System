<?php

namespace App\Services\Integration;

use App\Exceptions\CircuitOpenException;
use App\Models\ApiSetting;
use App\Models\Diagnosis;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Visit;
use App\Services\CircuitBreaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SATUSEHAT (FHIR) integration service.
 * Requirements: 14.1–14.9
 */
class SatuSehatService implements ExternalApiServiceInterface
{
    private ?ApiSetting $config   = null;
    private ?string $clientId     = null;
    private ?string $clientSecret = null;
    private ?string $endpoint     = null;
    private bool $testingMode     = true;

    private const CB_SERVICE = 'satusehat';

    public function __construct(
        private readonly MockApiService $mockService,
        private readonly FhirValidator $fhirValidator,
        private readonly CircuitBreaker $circuitBreaker,
    ) {
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        try {
            $this->config = ApiSetting::where('integration_name', 'satusehat')->first();
            if ($this->config) {
                $this->testingMode  = $this->config->isTestingMode();
                $this->endpoint     = $this->testingMode
                    ? ($this->config->sandbox_url ?? $this->config->endpoint_url)
                    : $this->config->endpoint_url;
                $this->clientId     = $this->config->consumer_key;
                $this->clientSecret = $this->config->consumer_secret;
            }
        } catch (\Exception $e) {
            Log::error('SatuSehatService: Failed to load config', ['error' => $e->getMessage()]);
        }
    }

    public function isTestingMode(): bool { return $this->testingMode; }

    private function validateOrFail(array $payload): ?array
    {
        $result = $this->fhirValidator->validate($payload);
        if (! $result['valid']) {
            $message = 'FHIR Resource tidak valid: ' . implode('; ', $result['errors']);
            Log::error('SatuSehatService: ' . $message, ['resourceType' => $payload['resourceType'] ?? 'unknown']);
            return ['success' => false, 'message' => $message, 'errors' => $result['errors']];
        }
        return null;
    }

    public function send(array $payload): array
    {
        if ($this->isTestingMode()) return $this->mockService->send($payload);
        if ($this->circuitBreaker->isOpen(self::CB_SERVICE)) {
            throw new CircuitOpenException(self::CB_SERVICE);
        }
        try {
            $result = Http::withHeaders($this->buildHeaders())->timeout(30)->post($this->endpoint, $payload)->json() ?? [];
            $this->circuitBreaker->recordSuccess(self::CB_SERVICE);
            return $result;
        } catch (\Exception $e) {
            Log::error('SatuSehatService::send failed', ['error' => $e->getMessage()]);
            $this->circuitBreaker->recordFailure(self::CB_SERVICE);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function testConnection(): array
    {
        if ($this->isTestingMode()) return $this->mockService->testConnection();
        if (empty($this->endpoint)) return ['success' => false, 'status_code' => null, 'message' => 'Endpoint tidak dikonfigurasi.'];
        try {
            $response = Http::withHeaders($this->buildHeaders())->timeout(10)->get($this->endpoint);
            return ['success' => $response->successful(), 'status_code' => $response->status(), 'message' => $response->successful() ? 'Koneksi berhasil.' : 'Gagal: '.$response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'status_code' => null, 'message' => $e->getMessage()];
        }
    }

    public function syncPatient(int $patientId): array
    {
        try {
            $patient = Patient::findOrFail($patientId);
            $payload = $this->buildFhirPatient($patient);
            if ($error = $this->validateOrFail($payload)) return $error;
            if ($this->isTestingMode()) return $this->mockService->send($payload);
            return Http::withHeaders($this->buildHeaders())->timeout(30)->post(rtrim($this->endpoint, '/').'/Patient', $payload)->json() ?? [];
        } catch (\Exception $e) {
            Log::error('SatuSehatService::syncPatient failed', ['patientId' => $patientId, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendEncounter(int $visitId): array
    {
        try {
            $visit   = Visit::with(['patient', 'doctor', 'poli'])->findOrFail($visitId);
            $payload = $this->buildFhirEncounter($visit);
            if ($error = $this->validateOrFail($payload)) return $error;
            if ($this->isTestingMode()) return $this->mockService->send($payload);
            return Http::withHeaders($this->buildHeaders())->timeout(30)->post(rtrim($this->endpoint, '/').'/Encounter', $payload)->json() ?? [];
        } catch (\Exception $e) {
            Log::error('SatuSehatService::sendEncounter failed', ['visitId' => $visitId, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendCondition(int $diagnosisId): array
    {
        try {
            $diagnosis = Diagnosis::with(['visit.patient', 'icd10Code'])->findOrFail($diagnosisId);
            $payload   = $this->buildFhirCondition($diagnosis);
            if ($error = $this->validateOrFail($payload)) return $error;
            if ($this->isTestingMode()) return $this->mockService->send($payload);
            return Http::withHeaders($this->buildHeaders())->timeout(30)->post(rtrim($this->endpoint, '/').'/Condition', $payload)->json() ?? [];
        } catch (\Exception $e) {
            Log::error('SatuSehatService::sendCondition failed', ['diagnosisId' => $diagnosisId, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendObservation(array $data): array
    {
        try {
            $payload = $this->buildFhirObservation($data);
            if ($error = $this->validateOrFail($payload)) return $error;
            if ($this->isTestingMode()) return $this->mockService->send($payload);
            return Http::withHeaders($this->buildHeaders())->timeout(30)->post(rtrim($this->endpoint, '/').'/Observation', $payload)->json() ?? [];
        } catch (\Exception $e) {
            Log::error('SatuSehatService::sendObservation failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendMedication(int $prescriptionId): array
    {
        try {
            $prescription = Prescription::with(['visit.patient', 'items.drug'])->findOrFail($prescriptionId);
            $payload      = $this->buildFhirMedication($prescription);
            if (isset($payload['resourceType']) && $payload['resourceType'] !== 'Bundle') {
                if ($error = $this->validateOrFail($payload)) return $error;
            }
            if ($this->isTestingMode()) return $this->mockService->send($payload);
            return Http::withHeaders($this->buildHeaders())->timeout(30)->post(rtrim($this->endpoint, '/').'/MedicationRequest', $payload)->json() ?? [];
        } catch (\Exception $e) {
            Log::error('SatuSehatService::sendMedication failed', ['prescriptionId' => $prescriptionId, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // FHIR Resource Builders

    private function buildFhirPatient(Patient $patient): array
    {
        return [
            'resourceType' => 'Patient',
            'identifier'   => [
                ['system' => 'https://fhir.kemkes.go.id/id/nik', 'value' => $patient->nik ?? ''],
                ['system' => 'https://fhir.kemkes.go.id/id/no-rm', 'value' => $patient->no_rm],
            ],
            'name'      => [['use' => 'official', 'text' => $patient->nama_lengkap]],
            'gender'    => $this->mapGender($patient->jenis_kelamin),
            'birthDate' => $patient->tanggal_lahir?->format('Y-m-d'),
        ];
    }

    private function buildFhirEncounter(Visit $visit): array
    {
        return [
            'resourceType' => 'Encounter',
            'identifier'   => [['system' => 'https://fhir.kemkes.go.id/id/no-rawat', 'value' => $visit->no_rawat]],
            'status'       => $this->mapVisitStatusToFhir($visit->status),
            'class'        => ['system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode', 'code' => 'AMB'],
            'subject'      => ['reference' => 'Patient/'.$visit->patient_id, 'display' => $visit->patient?->nama_lengkap],
            'period'       => ['start' => $visit->tanggal_kunjungan?->toIso8601String()],
        ];
    }

    private function buildFhirCondition(Diagnosis $diagnosis): array
    {
        return [
            'resourceType'   => 'Condition',
            'clinicalStatus' => ['coding' => [['system' => 'http://terminology.hl7.org/CodeSystem/condition-clinical', 'code' => 'active']]],
            'code'           => ['coding' => [['system' => 'http://hl7.org/fhir/sid/icd-10', 'code' => $diagnosis->icd10_code, 'display' => $diagnosis->icd10Code?->deskripsi ?? '']]],
            'subject'        => ['reference' => 'Patient/'.$diagnosis->visit?->patient_id],
            'encounter'      => ['reference' => 'Encounter/'.$diagnosis->visit_id],
        ];
    }

    private function buildFhirObservation(array $data): array
    {
        return [
            'resourceType' => 'Observation',
            'status'       => $data['status'] ?? 'final',
            'code'         => ['coding' => [['system' => $data['code_system'] ?? 'http://loinc.org', 'code' => $data['code'] ?? '', 'display' => $data['display'] ?? '']]],
            'subject'      => ['reference' => 'Patient/'.($data['patient_id'] ?? '')],
        ];
    }

    private function buildFhirMedication(Prescription $prescription): array
    {
        $items = $prescription->items->map(fn($item) => [
            'resourceType'              => 'MedicationRequest',
            'status'                    => 'active',
            'intent'                    => 'order',
            'medicationCodeableConcept' => ['coding' => [['system' => 'https://fhir.kemkes.go.id/id/kfa', 'code' => $item->drug?->kode ?? '', 'display' => $item->drug?->nama ?? '']]],
            'subject'                   => ['reference' => 'Patient/'.$prescription->visit?->patient_id],
            'encounter'                 => ['reference' => 'Encounter/'.$prescription->visit_id],
        ])->toArray();

        return count($items) === 1 ? $items[0] : ['resourceType' => 'Bundle', 'type' => 'transaction', 'entry' => array_map(fn($i) => ['resource' => $i], $items)];
    }

    private function mapGender(string $jenisKelamin): string
    {
        return match (strtolower($jenisKelamin)) {
            'l', 'laki-laki' => 'male',
            'p', 'perempuan' => 'female',
            default          => 'unknown',
        };
    }

    private function mapVisitStatusToFhir(string $status): string
    {
        return match ($status) {
            'menunggu'          => 'planned',
            'dipanggil'         => 'arrived',
            'dalam_pemeriksaan' => 'in-progress',
            'selesai'           => 'finished',
            default             => 'unknown',
        };
    }

    private function buildHeaders(): array
    {
        return ['Content-Type' => 'application/json', 'Accept' => 'application/json', 'Authorization' => 'Bearer '.$this->getAccessToken()];
    }

    private function getAccessToken(): string
    {
        if (empty($this->clientId) || empty($this->clientSecret)) return '';
        try {
            $tokenUrl = $this->config?->additional_params['token_url'] ?? 'https://api-satusehat.kemkes.go.id/oauth2/v1/accesstoken';
            return Http::asForm()->timeout(10)->post($tokenUrl, ['client_id' => $this->clientId, 'client_secret' => $this->clientSecret])->json('access_token') ?? '';
        } catch (\Exception $e) {
            Log::error('SatuSehatService: Failed to get access token', ['error' => $e->getMessage()]);
            return '';
        }
    }
}
