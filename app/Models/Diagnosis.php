<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnosis extends Model
{
    protected $fillable = ['visit_id', 'icd10_code', 'is_primary'];
    protected $casts    = ['is_primary' => 'boolean'];

    public function visit(): BelongsTo     { return $this->belongsTo(Visit::class); }
    public function icd10Code(): BelongsTo { return $this->belongsTo(Icd10Code::class, 'icd10_code', 'kode'); }
}
