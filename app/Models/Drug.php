<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drug extends Model
{
    protected $fillable = [
        'kode', 'nama', 'drug_category_id', 'drug_unit_id',
        'supplier_id', 'harga_beli', 'harga_jual', 'is_active',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DrugCategory::class, 'drug_category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(DrugUnit::class, 'drug_unit_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(DrugStock::class);
    }
}
