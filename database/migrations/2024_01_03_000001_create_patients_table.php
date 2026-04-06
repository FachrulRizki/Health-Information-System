<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm')->unique();
            $table->string('nama_lengkap');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat')->nullable();
            $table->text('nik_encrypted')->nullable();
            $table->text('no_telepon_encrypted')->nullable();
            $table->enum('jenis_penjamin', ['umum', 'bpjs', 'asuransi'])->default('umum');
            $table->string('no_bpjs')->nullable();
            $table->string('no_polis_asuransi')->nullable();
            $table->string('nama_asuransi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
