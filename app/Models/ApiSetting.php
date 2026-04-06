<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ApiSetting extends Model
{
    protected $fillable = [
        'integration_name',
        'endpoint_url',
        'sandbox_url',
        'consumer_key_encrypted',
        'consumer_secret_encrypted',
        'mode',
        'additional_params',
        'is_active',
    ];

    protected $casts = [
        'additional_params' => 'array',
        'is_active'         => 'boolean',
    ];

    public function getConsumerKeyAttribute(): ?string
    {
        if (empty($this->consumer_key_encrypted)) {
            return null;
        }
        try {
            return Crypt::decrypt($this->consumer_key_encrypted);
        } catch (\Exception) {
            return null;
        }
    }

    public function getConsumerSecretAttribute(): ?string
    {
        if (empty($this->consumer_secret_encrypted)) {
            return null;
        }
        try {
            return Crypt::decrypt($this->consumer_secret_encrypted);
        } catch (\Exception) {
            return null;
        }
    }

    public function isTestingMode(): bool
    {
        return $this->mode === 'testing';
    }
}
