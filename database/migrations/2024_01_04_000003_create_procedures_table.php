<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->string('icd9cm_code');
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->foreign('icd9cm_code')->references('kode')->on('icd9cm_codes')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
