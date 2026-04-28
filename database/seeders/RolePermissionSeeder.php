<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',

            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permission management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // Master data - departemen
            'view departemen',
            'create departemen',
            'edit departemen',
            'delete departemen',

            // Master data - food
            'view food',
            'create food',
            'edit food',
            'delete food',

            // Saldo
            'view saldo',
            'create saldo transaction',

            // Event
            'view events',
            'create events',
            'edit events',
            'delete events',
            'approve event departemen',
            'process event umum',
            'approve event umum',
            'close event creator',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $karyawanRole = Role::firstOrCreate(['name' => 'Karyawan']);

        $adminRole->syncPermissions(Permission::all());
        $managerRole->syncPermissions([
            'view dashboard',
            'view events',
            'create events',
            'edit events',
            'delete events',
            'approve event departemen',
            'approve event umum',
            'close event creator',
        ]);
        $karyawanRole->syncPermissions([
            'view dashboard',
            'view events',
            'create events',
            'edit events',
            'delete events',
            'process event umum',
            'close event creator',
        ]);

        $admin = User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Admin');
    }
}
