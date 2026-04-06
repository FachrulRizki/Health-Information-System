<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->text('pharmacy_notes')->nullable()->after('prescribed_by');
        });

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('instructions');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('pharmacy_notes');
        });
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
