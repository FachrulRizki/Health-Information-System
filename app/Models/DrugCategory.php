<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DrugCategory extends Model
{
    protected $fillable = ['nama'];

    public function drugs(): HasMany
    {
        return $this->hasMany(Drug::class);
    }
}
