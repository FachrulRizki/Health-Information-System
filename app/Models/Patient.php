<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Patient extends Model
{
    protected $fillable = [
        'no_rm', 'nama_lengkap', 'tanggal_lahir', 'jenis_kelamin', 'alamat',
        'nik_encrypted', 'no_telepon_encrypted', 'jenis_penjamin',
        'no_bpjs', 'no_polis_asuransi', 'nama_asuransi',
    ];

    protected $casts = ['tanggal_lahir' => 'date'];

    public function getNikAttribute(): ?string
    {
        if (empty($this->nik_encrypted)) return null;
        try { return Crypt::decryptString($this->nik_encrypted); } catch (\Exception) { return null; }
    }

    public function getNoTeleponAttribute(): ?string
    {
        if (empty($this->no_telepon_encrypted)) return null;
        try { return Crypt::decryptString($this->no_telepon_encrypted); } catch (\Exception) { return null; }
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
