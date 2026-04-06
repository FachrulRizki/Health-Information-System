<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrugStock extends Model
{
    protected $fillable = ['drug_id', 'quantity', 'expiry_date', 'batch_number', 'minimum_stock'];

    protected $casts = [
        'quantity'      => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'expiry_date'   => 'date',
    ];

    public function drug(): BelongsTo { return $this->belongsTo(Drug::class); }

    public function isExpired(): bool
    {
        return $this->expiry_date->lt(Carbon::today());
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_stock;
    }

    public function isNearExpiry(int $days = 30): bool
    {
        return $this->expiry_date->gt(Carbon::today())
            && $this->expiry_date->lte(Carbon::today()->addDays($days));
    }
}
