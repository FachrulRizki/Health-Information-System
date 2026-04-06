<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->cascadeOnDelete();
            $table->foreignId('poli_id')->constrained('polis')->cascadeOnDelete();
            $table->unsignedInteger('queue_number');
            $table->enum('status', ['menunggu', 'dipanggil', 'dalam_pemeriksaan', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_entries');
    }
};
