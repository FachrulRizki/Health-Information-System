<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    protected $fillable = ['visit_id', 'subjective', 'objective', 'assessment', 'plan', 'created_by'];

    public function visit(): BelongsTo   { return $this->belongsTo(Visit::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
