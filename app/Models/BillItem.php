<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    protected $fillable = ['bill_id', 'item_type', 'item_id', 'item_name', 'unit_price', 'quantity', 'subtotal'];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity'   => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    public function bill(): BelongsTo { return $this->belongsTo(Bill::class); }
}
