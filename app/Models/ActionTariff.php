<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionTariff extends Model
{
    protected $fillable = ['action_master_id', 'jenis_penjamin', 'tarif'];

    protected $casts = ['tarif' => 'decimal:2'];

    public function actionMaster(): BelongsTo
    {
        return $this->belongsTo(ActionMaster::class);
    }
}
