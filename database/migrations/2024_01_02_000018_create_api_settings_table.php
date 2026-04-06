<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('integration_name')->unique();
            $table->string('endpoint_url');
            $table->string('sandbox_url')->nullable();
            $table->text('consumer_key_encrypted')->nullable();
            $table->text('consumer_secret_encrypted')->nullable();
            $table->enum('mode', ['testing', 'production'])->default('testing');
            $table->json('additional_params')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_settings');
    }
};
