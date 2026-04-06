<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LabRequest extends Model
{
    protected $fillable = ['visit_id', 'examination_type_id', 'status', 'requested_by', 'sample_taken_at', 'sample_taken_by'];
    protected $casts    = ['sample_taken_at' => 'datetime'];

    public function visit(): BelongsTo           { return $this->belongsTo(Visit::class); }
    public function examinationType(): BelongsTo { return $this->belongsTo(ExaminationType::class); }
    public function requestedBy(): BelongsTo     { return $this->belongsTo(User::class, 'requested_by'); }
    public function sampleTakenBy(): BelongsTo   { return $this->belongsTo(User::class, 'sample_taken_by'); }
    public function result(): HasOne             { return $this->hasOne(LabResult::class); }
}
