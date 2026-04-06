<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationType extends Model
{
    protected $fillable = ['kode', 'nama', 'kategori', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
