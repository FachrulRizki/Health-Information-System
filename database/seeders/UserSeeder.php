<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed default accounts for all roles.
     */
    public function run(): void
    {
        $users = [
            ['username' => 'admin',       'password' => Hash::make('Admin@12345'),       'role' => 'admin'],
            ['username' => 'dokter',      'password' => Hash::make('Dokter@12345'),      'role' => 'dokter'],
            ['username' => 'perawat',     'password' => Hash::make('Perawat@12345'),     'role' => 'perawat'],
            ['username' => 'farmasi',     'password' => Hash::make('Farmasi@12345'),     'role' => 'farmasi'],
            ['username' => 'kasir',       'password' => Hash::make('Kasir@12345'),       'role' => 'kasir'],
            ['username' => 'pendaftaran', 'password' => Hash::make('Pendaftaran@12345'), 'role' => 'petugas_pendaftaran'],
            ['username' => 'manajemen',   'password' => Hash::make('Manajemen@12345'),   'role' => 'manajemen'],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                array_merge($userData, ['is_active' => true, 'failed_login_count' => 0])
            );
        }
    }
}
