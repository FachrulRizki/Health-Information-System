<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PatientService
{
    /**
     * Generate a unique NoRM in format RM-YYYYMMDD-XXXXX.
     */
    public function generateNoRM(): string
    {
        return DB::transaction(function () {
            $date   = date('Ymd');
            $prefix = "RM-{$date}-";

            $last = Patient::where('no_rm', 'like', $prefix . '%')
                ->orderBy('no_rm', 'desc')
                ->lockForUpdate()
                ->first();

            $counter = $last ? ((int) explode('-', $last->no_rm)[2]) + 1 : 1;

            do {
                $noRM   = $prefix . str_pad($counter, 5, '0', STR_PAD_LEFT);
                $exists = Patient::where('no_rm', $noRM)->exists();
                if ($exists) $counter++;
            } while ($exists);

            return $noRM;
        });
    }

    /**
     * Generate a unique NoRawat in format RWT-YYYYMMDD-XXXXX.
     */
    public function generateNoRawat(): string
    {
        return DB::transaction(function () {
            $date   = date('Ymd');
            $prefix = "RWT-{$date}-";

            $last = Visit::where('no_rawat', 'like', $prefix . '%')
                ->orderBy('no_rawat', 'desc')
                ->lockForUpdate()
                ->first();

            $counter = $last ? ((int) explode('-', $last->no_rawat)[2]) + 1 : 1;

            do {
                $noRawat = $prefix . str_pad($counter, 5, '0', STR_PAD_LEFT);
                $exists  = Visit::where('no_rawat', $noRawat)->exists();
                if ($exists) $counter++;
            } while ($exists);

            return $noRawat;
        });
    }

    /**
     * Create a new patient with encrypted sensitive fields.
     */
    public function createPatient(array $data): Patient
    {
        return DB::transaction(function () use ($data) {
            $patientData = array_merge($data, [
                'no_rm'                => $this->generateNoRM(),
                'nik_encrypted'        => isset($data['nik']) ? Crypt::encryptString($data['nik']) : null,
                'no_telepon_encrypted' => isset($data['no_telepon']) ? Crypt::encryptString($data['no_telepon']) : null,
            ]);

            unset($patientData['nik'], $patientData['no_telepon']);

            return Patient::create($patientData);
        });
    }

    /**
     * Create a new visit for a patient.
     */
    public function createVisit(int $patientId, array $data): Visit
    {
        return DB::transaction(function () use ($patientId, $data) {
            return Visit::create(array_merge($data, [
                'patient_id' => $patientId,
                'no_rawat'   => $this->generateNoRawat(),
            ]));
        });
    }

    /**
     * Search patients by nama_lengkap or no_rm.
     */
    public function searchPatients(string $query): Collection
    {
        return Patient::where('nama_lengkap', 'like', "%{$query}%")
            ->orWhere('no_rm', 'like', "%{$query}%")
            ->orderBy('nama_lengkap')
            ->get();
    }
}
