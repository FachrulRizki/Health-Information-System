<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubSpecialization extends Model
{
    protected $fillable = ['specialization_id', 'kode', 'nama'];

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
}
