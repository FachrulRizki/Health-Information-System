<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('no_rawat')->unique();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('restrict');
            $table->foreignId('poli_id')->constrained('polis')->onDelete('restrict');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('jenis_penjamin', ['umum', 'bpjs', 'asuransi']);
            $table->string('no_sep')->nullable();
            $table->enum('status', ['pendaftaran', 'menunggu', 'dipanggil', 'dalam_pemeriksaan', 'farmasi', 'kasir', 'selesai'])->default('pendaftaran');
            $table->date('tanggal_kunjungan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
