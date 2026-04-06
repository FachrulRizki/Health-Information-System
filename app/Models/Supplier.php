<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = ['nama', 'alamat', 'no_telepon', 'email', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function drugs(): HasMany
    {
        return $this->hasMany(Drug::class);
    }
}
