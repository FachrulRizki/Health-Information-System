<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bed extends Model
{
    protected $fillable = ['room_id', 'kode_bed', 'status', 'current_patient_id'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function currentPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'current_patient_id');
    }
}
