<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions
        $permissions = [
            'view sales', 'manage sales',
            'view products', 'manage products',
            'view categories', 'manage categories',
            'view attributes', 'manage attributes',
            'view users', 'manage users',
            'manage business settings'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Define Roles and Assign Permissions
        $admin = Role::findOrCreate('admin');
        $admin->givePermissionTo(Permission::all());

        $staff = Role::findOrCreate('staff');
        $staff->givePermissionTo([
            'view sales', 'manage sales',
            'view products', 'manage products',
            'view categories', 'manage categories',
            'view attributes', 'manage attributes'
        ]);

        $customer = Role::findOrCreate('customer');
        // Customers usually have limited web-based management access

        // Optional: Sync existing users from 'role' column to Spatie roles
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role) {
                $user->assignRole($user->role);
            }
        }
    }
}
