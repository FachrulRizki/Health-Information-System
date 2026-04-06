<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RadiologyRequest extends Model
{
    protected $fillable = ['visit_id', 'examination_type_id', 'status', 'requested_by'];

    public function visit(): BelongsTo           { return $this->belongsTo(Visit::class); }
    public function examinationType(): BelongsTo { return $this->belongsTo(ExaminationType::class); }
    public function requestedBy(): BelongsTo     { return $this->belongsTo(User::class, 'requested_by'); }
    public function result(): HasOne             { return $this->hasOne(RadiologyResult::class); }
}
