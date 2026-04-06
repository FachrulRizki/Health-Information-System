<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Procedure extends Model
{
    protected $fillable = ['visit_id', 'icd9cm_code', 'performed_by'];

    public function visit(): BelongsTo      { return $this->belongsTo(Visit::class); }
    public function icd9cmCode(): BelongsTo { return $this->belongsTo(Icd9cmCode::class, 'icd9cm_code', 'kode'); }
    public function performer(): BelongsTo  { return $this->belongsTo(User::class, 'performed_by'); }
}
