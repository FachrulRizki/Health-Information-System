<?php

namespace App\Models;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poli extends Model
{
    protected $fillable = ['kode_poli', 'nama_poli', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function subPolis(): HasMany
    {
        return $this->hasMany(SubPoli::class);
    }

    public function doctorSchedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }
}
