<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadiologyResult extends Model
{
    protected $fillable = ['radiology_request_id', 'result_notes', 'file_path', 'created_by'];

    public function radiologyRequest(): BelongsTo { return $this->belongsTo(RadiologyRequest::class, 'radiology_request_id'); }
    public function createdBy(): BelongsTo        { return $this->belongsTo(User::class, 'created_by'); }
}
