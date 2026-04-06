<?php

namespace Database\Seeders;

use App\Models\ApiSetting;
use Illuminate\Database\Seeder;

class ApiSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $integrations = [
            ['integration_name' => 'bpjs_vclaim', 'endpoint_url' => 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest', 'sandbox_url' => 'https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev'],
            ['integration_name' => 'satusehat',   'endpoint_url' => 'https://api-satusehat.kemkes.go.id',               'sandbox_url' => 'https://api-satusehat-stg.dto.kemkes.go.id'],
            ['integration_name' => 'aplicare',    'endpoint_url' => 'https://api.aplicare.id',                          'sandbox_url' => 'https://sandbox.aplicare.id'],
            ['integration_name' => 'kemenkes',    'endpoint_url' => 'https://api.kemkes.go.id/rl',                      'sandbox_url' => 'https://sandbox.kemkes.go.id/rl'],
            ['integration_name' => 'ina_cbgs',    'endpoint_url' => 'https://ina-cbg.kemkes.go.id/api',                 'sandbox_url' => 'https://ina-cbg-dev.kemkes.go.id/api'],
        ];

        foreach ($integrations as $integration) {
            ApiSetting::updateOrCreate(
                ['integration_name' => $integration['integration_name']],
                array_merge($integration, ['mode' => 'testing', 'is_active' => true])
            );
        }
    }
}
