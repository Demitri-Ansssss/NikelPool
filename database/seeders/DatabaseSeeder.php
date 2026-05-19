<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat roles
        $adminRole    = Role::firstOrCreate(['name' => 'admin']);
        $approverRole = Role::firstOrCreate(['name' => 'approver']);
        $staffRole    = Role::firstOrCreate(['name' => 'staff']);

        // Buat Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@nikelpool.com'],
            [
                'name'        => 'Super Admin',
                'password'    => bcrypt('password'),
                'employee_id' => 'EMP-001',
                'department'  => 'IT',
                'position'    => 'System Administrator',
                'phone'       => '08111111111',
                'is_active'   => true,
            ]
        );
        $admin->assignRole($adminRole);

        // Approver Level 1 (Kepala Seksi)
        $approver1 = User::firstOrCreate(
            ['email' => 'kasie@nikelpool.com'],
            [
                'name'        => 'Budi Santoso',
                'password'    => bcrypt('password'),
                'employee_id' => 'EMP-002',
                'department'  => 'Operasional',
                'position'    => 'Kepala Seksi',
                'phone'       => '08222222222',
                'is_active'   => true,
            ]
        );
        $approver1->assignRole($approverRole);

        // Approver Level 2 (Kepala Bagian)
        $approver2 = User::firstOrCreate(
            ['email' => 'kabag@nikelpool.com'],
            [
                'name'        => 'Siti Rahayu',
                'password'    => bcrypt('password'),
                'employee_id' => 'EMP-003',
                'department'  => 'Operasional',
                'position'    => 'Kepala Bagian',
                'phone'       => '08333333333',
                'is_active'   => true,
            ]
        );
        $approver2->assignRole($approverRole);

        // Buat Regions
        Region::insert([
            ['name' => 'Kantor Pusat',    'type' => 'pusat',   'city' => 'Jakarta',   'province' => 'DKI Jakarta',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kantor Cabang',   'type' => 'cabang',  'city' => 'Makassar',  'province' => 'Sulawesi Selatan','is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tambang Site A',  'type' => 'tambang', 'city' => 'Morowali',  'province' => 'Sulawesi Tengah', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tambang Site B',  'type' => 'tambang', 'city' => 'Konawe',    'province' => 'Sulawesi Tenggara','is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tambang Site C',  'type' => 'tambang', 'city' => 'Kolaka',    'province' => 'Sulawesi Tenggara','is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tambang Site D',  'type' => 'tambang', 'city' => 'Halmahera', 'province' => 'Maluku Utara',   'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tambang Site E',  'type' => 'tambang', 'city' => 'Pomalaa',   'province' => 'Sulawesi Tenggara','is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tambang Site F',  'type' => 'tambang', 'city' => 'Soroako',   'province' => 'Sulawesi Selatan','is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}   