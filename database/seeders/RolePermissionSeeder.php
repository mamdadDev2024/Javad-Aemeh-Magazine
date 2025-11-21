<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Permission::$permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        foreach (Role::$roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->givePermissionTo($permissions);
        }

        $admin = User::create([
            'username' => 'admin',
            'name' => 'ادمین',
            'email' => 'hashemimohammadhosein08@gmail.com',
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'number' => 9903008746,
        ]);
        $admin->assignRole('super admin');
    }
}
