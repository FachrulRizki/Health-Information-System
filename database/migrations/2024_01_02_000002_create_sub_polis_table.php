<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_polis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id')->constrained('polis')->onDelete('restrict');
            $table->string('kode_sub_poli');
            $table->string('nama_sub_poli');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_polis');
    }
};
