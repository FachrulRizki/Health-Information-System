<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inpatient_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->cascadeOnDelete();
            $table->foreignId('bed_id')->nullable()->constrained('beds')->nullOnDelete();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status_pulang', ['dirawat', 'pulang_atas_permintaan', 'pulang_sembuh', 'meninggal', 'dirujuk'])->default('dirawat');
            $table->text('catatan_keperawatan')->nullable();
            $table->text('penilaian_medis')->nullable();
            $table->text('asesmen_awal')->nullable();
            $table->text('resume_pulang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inpatient_records');
    }
};
