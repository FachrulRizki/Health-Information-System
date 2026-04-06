<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubPoli extends Model
{
    protected $fillable = ['poli_id', 'kode_sub_poli', 'nama_sub_poli', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }
}
