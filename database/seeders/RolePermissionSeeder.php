<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'delete_tasks',
            'assign_tasks',
            'view_reports',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $roles = [
            'admin' => $permissions,
            'admin' => [
                'view_dashboard',
                'manage_users',
                'view_tasks',
                'create_tasks',
                'edit_tasks',
                'delete_tasks',
                'assign_tasks',
                'view_reports',
                'manage_settings',
            ],
            'user' => [
                'view_dashboard',
                'view_tasks',
                'create_tasks',
                'edit_tasks',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($rolePermissions);
        }
    }
} 