<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('kode_dokter')->unique();
            $table->string('nama_dokter');
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->onDelete('set null');
            $table->foreignId('sub_specialization_id')->nullable()->constrained('sub_specializations')->onDelete('set null');
            $table->string('no_sip')->nullable();
            $table->string('no_telepon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
