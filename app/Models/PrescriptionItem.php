<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    protected $fillable = ['prescription_id', 'drug_id', 'quantity', 'dosage', 'instructions', 'status'];

    protected $casts = ['quantity' => 'decimal:2'];

    public function prescription(): BelongsTo { return $this->belongsTo(Prescription::class); }
    public function drug(): BelongsTo         { return $this->belongsTo(Drug::class); }
}
