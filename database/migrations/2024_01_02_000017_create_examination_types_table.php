<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examination_types', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->enum('kategori', ['lab', 'radiologi', 'ekg', 'usg', 'ctg']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examination_types');
    }
};
