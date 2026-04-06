<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabResult extends Model
{
    protected $fillable = ['lab_request_id', 'result_data', 'created_by'];
    protected $casts    = ['result_data' => 'array'];

    public function labRequest(): BelongsTo { return $this->belongsTo(LabRequest::class); }
    public function createdBy(): BelongsTo  { return $this->belongsTo(User::class, 'created_by'); }
}
