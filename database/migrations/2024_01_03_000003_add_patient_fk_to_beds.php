<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            $table->foreign('current_patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('beds', function (Blueprint $table) {
            $table->dropForeign(['current_patient_id']);
        });
    }
};
