<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class QueueEntry extends Model
{
    protected $fillable = ['visit_id', 'poli_id', 'queue_number', 'status'];

    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
    public function poli(): BelongsTo  { return $this->belongsTo(Poli::class); }

    public function patient(): HasOneThrough
    {
        return $this->hasOneThrough(Patient::class, Visit::class, 'id', 'id', 'visit_id', 'patient_id');
    }
}
