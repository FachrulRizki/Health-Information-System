<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InpatientRecord extends Model
{
    protected $fillable = [
        'visit_id', 'bed_id', 'tanggal_masuk', 'tanggal_keluar',
        'status_pulang', 'catatan_keperawatan', 'penilaian_medis',
        'asesmen_awal', 'resume_pulang',
    ];

    protected $casts = [
        'tanggal_masuk'  => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
    public function bed(): BelongsTo   { return $this->belongsTo(Bed::class); }
}
