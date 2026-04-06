<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', [
                'admin',
                'dokter',
                'perawat',
                'farmasi',
                'kasir',
                'petugas_pendaftaran',
                'manajemen',
            ]);
            $table->boolean('is_active')->default(true);
            $table->timestamp('locked_until')->nullable();
            $table->integer('failed_login_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
