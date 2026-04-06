<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drug_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drug_id')->constrained('drugs');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->date('expiry_date');
            $table->string('batch_number')->nullable();
            $table->decimal('minimum_stock', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_stocks');
    }
};
