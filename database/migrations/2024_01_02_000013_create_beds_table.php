<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('restrict');
            $table->string('kode_bed');
            $table->enum('status', ['tersedia', 'terisi', 'dalam_perawatan', 'tidak_aktif'])->default('tersedia');
            // current_patient_id will reference patients table (created later)
            $table->unsignedBigInteger('current_patient_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
