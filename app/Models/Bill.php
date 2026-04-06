<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    protected $fillable = ['visit_id', 'total_amount', 'payment_method', 'status', 'bpjs_claim_status'];

    protected $casts = ['total_amount' => 'decimal:2'];

    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
    public function items(): HasMany   { return $this->hasMany(BillItem::class); }
}
