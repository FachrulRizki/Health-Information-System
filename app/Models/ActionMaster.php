<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActionMaster extends Model
{
    protected $fillable = ['kode', 'nama', 'icd9cm_code'];

    public function tariffs(): HasMany
    {
        return $this->hasMany(ActionTariff::class);
    }
}
