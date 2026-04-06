<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('action_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_master_id')->constrained('action_masters')->onDelete('restrict');
            $table->enum('jenis_penjamin', ['umum', 'bpjs', 'asuransi']);
            $table->decimal('tarif', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['action_master_id', 'jenis_penjamin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_tariffs');
    }
};
