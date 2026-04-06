<?php

namespace App\Services;

use App\Events\QueueStatusUpdated;
use App\Models\Diagnosis;
use App\Models\Icd10Code;
use App\Models\Icd9cmCode;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Procedure;
use App\Models\QueueEntry;
use App\Models\Visit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RMEService
{
    /**
     * Save SOAP data for a visit. Validates at least 1 ICD-10 diagnosis.
     *
     * @throws ValidationException
     */
    public function saveSOAP(int $visitId, array $data): MedicalRecord
    {
        if (empty($data['diagnoses'])) {
            throw ValidationException::withMessages([
                'diagnoses' => ['Minimal satu diagnosa ICD-10 harus dipilih.'],
            ]);
        }

        return DB::transaction(function () use ($visitId, $data) {
            $record = MedicalRecord::updateOrCreate(
                ['visit_id' => $visitId],
                [
                    'subjective' => $data['subjective'] ?? null,
                    'objective'  => $data['objective']  ?? null,
                    'assessment' => $data['assessment'] ?? null,
                    'plan'       => $data['plan']       ?? null,
                    'created_by' => auth()->id(),
                ]
            );

            Diagnosis::where('visit_id', $visitId)->delete();
            foreach ($data['diagnoses'] as $index => $code) {
                Diagnosis::create(['visit_id' => $visitId, 'icd10_code' => $code, 'is_primary' => $index === 0]);
            }

            Procedure::where('visit_id', $visitId)->delete();
            foreach ($data['procedures'] ?? [] as $code) {
                Procedure::create(['visit_id' => $visitId, 'icd9cm_code' => $code, 'performed_by' => auth()->id()]);
            }

            // Update visit status based on whether a prescription exists (Req 4.5)
            // If there's a pending prescription → farmasi, otherwise → kasir
            $hasPrescription = Prescription::where('visit_id', $visitId)
                ->whereIn('status', ['pending', 'validated'])
                ->exists();

            $nextStatus = $hasPrescription ? 'farmasi' : 'kasir';
            $visit = Visit::find($visitId);
            if ($visit && $visit->status === 'dalam_pemeriksaan') {
                $visit->update(['status' => $nextStatus]);

                // Fire QueueStatusUpdated so real-time display reflects the change
                $queueEntry = QueueEntry::where('visit_id', $visitId)->first();
                if ($queueEntry) {
                    event(new QueueStatusUpdated($queueEntry->fresh()));
                }
            }

            return $record;
        });
    }

    public function getPatientHistory(int $patientId): Collection
    {
        return Visit::with(['medicalRecord', 'diagnoses.icd10Code', 'procedures.icd9cmCode', 'poli', 'doctor'])
            ->where('patient_id', $patientId)
            ->whereHas('medicalRecord')
            ->orderByDesc('tanggal_kunjungan')
            ->get();
    }

    public function searchIcd10(string $query): Collection
    {
        return Icd10Code::where('kode', 'LIKE', "%{$query}%")
            ->orWhere('deskripsi', 'LIKE', "%{$query}%")
            ->limit(20)->get();
    }

    public function searchIcd9cm(string $query): Collection
    {
        return Icd9cmCode::where('kode', 'LIKE', "%{$query}%")
            ->orWhere('deskripsi', 'LIKE', "%{$query}%")
            ->limit(20)->get();
    }

    /**
     * Validate SKDP (Surat Kontrol Dalam Poli) requirements.
     *
     * @throws ValidationException
     */
    public function validateSKDP(int $visitId, array $data): void
    {
        $visit = Visit::findOrFail($visitId);

        if (empty($visit->no_sep)) {
            throw ValidationException::withMessages([
                'no_sep' => ['SEP belum terbit untuk kunjungan ini.'],
            ]);
        }

        if (empty($data['tanggal_rencana_kontrol'])) {
            throw ValidationException::withMessages([
                'tanggal_rencana_kontrol' => ['Tanggal rencana kontrol harus diisi.'],
            ]);
        }

        $tanggal = \Carbon\Carbon::parse($data['tanggal_rencana_kontrol']);
        if (!$tanggal->isFuture()) {
            throw ValidationException::withMessages([
                'tanggal_rencana_kontrol' => ['Tanggal rencana kontrol harus berupa tanggal yang akan datang.'],
            ]);
        }

        if (empty($data['specialization_id'])) {
            throw ValidationException::withMessages([
                'specialization_id' => ['Spesialis/sub spesialis harus dipilih.'],
            ]);
        }

        if (empty($data['dpjp_doctor_id'])) {
            throw ValidationException::withMessages([
                'dpjp_doctor_id' => ['DPJP harus dipilih.'],
            ]);
        }
    }

    /**
     * Create SKDP after validation.
     * Actual BPJS VClaim integration will be handled in task 10.
     */
    public function createSKDP(int $visitId, array $data): array
    {
        $this->validateSKDP($visitId, $data);

        return [
            'visit_id'                 => $visitId,
            'tanggal_rencana_kontrol'  => $data['tanggal_rencana_kontrol'],
            'specialization_id'        => $data['specialization_id'],
            'dpjp_doctor_id'           => $data['dpjp_doctor_id'],
        ];
    }
}
