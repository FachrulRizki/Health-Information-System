<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->cascadeOnDelete();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('payment_method', ['umum', 'bpjs', 'asuransi'])->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->string('bpjs_claim_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
