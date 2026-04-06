<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    protected $fillable = [
        'no_rawat', 'patient_id', 'poli_id', 'doctor_id', 'user_id',
        'jenis_penjamin', 'no_sep', 'status', 'tanggal_kunjungan',
    ];

    protected $casts = ['tanggal_kunjungan' => 'date'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function poli(): BelongsTo    { return $this->belongsTo(Poli::class); }
    public function doctor(): BelongsTo  { return $this->belongsTo(Doctor::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }

    public function medicalRecord(): HasOne   { return $this->hasOne(MedicalRecord::class); }
    public function inpatientRecord(): HasOne { return $this->hasOne(InpatientRecord::class); }
    public function bill(): HasOne           { return $this->hasOne(Bill::class); }
    public function queueEntry(): HasOne     { return $this->hasOne(QueueEntry::class); }

    public function diagnoses(): HasMany    { return $this->hasMany(Diagnosis::class); }
    public function procedures(): HasMany   { return $this->hasMany(Procedure::class); }
    public function prescriptions(): HasMany { return $this->hasMany(Prescription::class); }
}
