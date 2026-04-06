<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends Model
{
    protected $fillable = ['kode', 'nama'];

    public function subSpecializations(): HasMany
    {
        return $this->hasMany(SubSpecialization::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
}
