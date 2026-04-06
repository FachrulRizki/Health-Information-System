<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->string('icd10_code');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('icd10_code')->references('kode')->on('icd10_codes')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};
