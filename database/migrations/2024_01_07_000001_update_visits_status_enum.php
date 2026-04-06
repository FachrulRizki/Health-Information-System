<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // MySQL/MariaDB: use MODIFY COLUMN to change enum values
            DB::statement("ALTER TABLE visits MODIFY COLUMN status ENUM(
                'pendaftaran',
                'menunggu',
                'dipanggil',
                'dalam_pemeriksaan',
                'farmasi',
                'kasir',
                'selesai'
            ) NOT NULL DEFAULT 'pendaftaran'");

            // Migrate existing data: map old values to new canonical values
            DB::statement("UPDATE visits SET status = 'menunggu' WHERE status = 'antrian'");
            DB::statement("UPDATE visits SET status = 'dalam_pemeriksaan' WHERE status = 'pemeriksaan'");
        } else {
            // SQLite (used in testing): string column already accepts any value, just migrate data
            DB::statement("UPDATE visits SET status = 'menunggu' WHERE status = 'antrian'");
            DB::statement("UPDATE visits SET status = 'dalam_pemeriksaan' WHERE status = 'pemeriksaan'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Revert data first
        DB::statement("UPDATE visits SET status = 'antrian' WHERE status = 'menunggu'");
        DB::statement("UPDATE visits SET status = 'pemeriksaan' WHERE status = 'dalam_pemeriksaan'");
        DB::statement("UPDATE visits SET status = 'pendaftaran' WHERE status = 'dipanggil'");

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE visits MODIFY COLUMN status ENUM(
                'pendaftaran',
                'antrian',
                'pemeriksaan',
                'farmasi',
                'kasir',
                'selesai'
            ) NOT NULL DEFAULT 'pendaftaran'");
        }
    }
};
