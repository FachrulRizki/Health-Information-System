<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiology_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radiology_request_id')->constrained('radiology_requests')->cascadeOnDelete();
            $table->text('result_notes')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_results');
    }
};
