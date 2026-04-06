<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = ['kode_kamar', 'nama_kamar', 'kelas', 'kapasitas', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }
}
