<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('examination_type_id')->constrained('examination_types');
            $table->enum('status', ['pending', 'sample_taken', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('requested_by')->constrained('users');
            $table->timestamp('sample_taken_at')->nullable();
            $table->foreignId('sample_taken_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_requests');
    }
};
