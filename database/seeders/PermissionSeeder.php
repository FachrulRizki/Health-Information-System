<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['menu_key' => 'registration',  'menu_label' => 'Pendaftaran',    'parent_key' => null, 'sort_order' => 1],
            ['menu_key' => 'admisi',        'menu_label' => 'Admisi',         'parent_key' => null, 'sort_order' => 2],
            ['menu_key' => 'queue',         'menu_label' => 'Antrian',        'parent_key' => null, 'sort_order' => 3],
            ['menu_key' => 'rme',           'menu_label' => 'Rawat Jalan',    'parent_key' => null, 'sort_order' => 4],
            ['menu_key' => 'inpatient',     'menu_label' => 'Rawat Inap',     'parent_key' => null, 'sort_order' => 5],
            ['menu_key' => 'lab',           'menu_label' => 'Laboratorium',   'parent_key' => null, 'sort_order' => 6],
            ['menu_key' => 'radiology',     'menu_label' => 'Radiologi',      'parent_key' => null, 'sort_order' => 7],
            ['menu_key' => 'pharmacy',      'menu_label' => 'Farmasi',        'parent_key' => null, 'sort_order' => 8],
            ['menu_key' => 'billing',       'menu_label' => 'Billing',        'parent_key' => null, 'sort_order' => 9],
            ['menu_key' => 'claims',        'menu_label' => 'Klaim BPJS',     'parent_key' => null, 'sort_order' => 10],
            ['menu_key' => 'report',        'menu_label' => 'Laporan',        'parent_key' => null, 'sort_order' => 11],
            ['menu_key' => 'master.data',   'menu_label' => 'Master Data',    'parent_key' => null, 'sort_order' => 12],
            ['menu_key' => 'admin',         'menu_label' => 'Sistem Admin',   'parent_key' => null, 'sort_order' => 13],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['menu_key' => $perm['menu_key']],
                $perm
            );
        }

        $rolePermissions = [
            'dokter'              => ['rme', 'inpatient', 'queue', 'lab', 'radiology'],
            'perawat'             => ['queue', 'rme', 'inpatient', 'admisi'],
            'farmasi'             => ['pharmacy'],
            'kasir'               => ['billing', 'claims'],
            'petugas_pendaftaran' => ['registration', 'admisi', 'queue'],
            'manajemen'           => ['report', 'billing'],
        ];

        foreach ($rolePermissions as $role => $menuKeys) {
            $users          = User::where('role', $role)->get();
            $allPermissions = Permission::all()->keyBy('menu_key');

            foreach ($users as $user) {
                foreach ($allPermissions as $key => $permission) {
                    UserPermission::updateOrCreate(
                        ['user_id' => $user->id, 'permission_id' => $permission->id],
                        ['is_granted' => in_array($key, $menuKeys)]
                    );
                }
            }
        }
    }
}
