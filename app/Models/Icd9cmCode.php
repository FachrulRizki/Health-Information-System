<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icd9cmCode extends Model
{
    protected $table = 'icd9cm_codes';

    protected $fillable = ['kode', 'deskripsi'];
}
