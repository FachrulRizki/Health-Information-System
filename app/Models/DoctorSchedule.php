<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id', 'poli_id', 'hari',
        'jam_mulai', 'jam_selesai', 'kuota', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    /**
     * Check if this schedule is full for a given date (Req 19.5).
     * Returns true when the registered queue count meets or exceeds the quota.
     */
    public function isFullForDate(string $date): bool
    {
        $filled = QueueEntry::whereHas(
            'visit',
            fn($q) => $q->whereDate('tanggal_kunjungan', $date)
                        ->where('poli_id', $this->poli_id)
                        ->where('doctor_id', $this->doctor_id)
        )->count();

        return $filled >= $this->kuota;
    }

    /**
     * Get remaining available slots for a given date.
     */
    public function availableSlotsForDate(string $date): int
    {
        $filled = QueueEntry::whereHas(
            'visit',
            fn($q) => $q->whereDate('tanggal_kunjungan', $date)
                        ->where('poli_id', $this->poli_id)
                        ->where('doctor_id', $this->doctor_id)
        )->count();

        return max(0, $this->kuota - $filled);
    }
}
