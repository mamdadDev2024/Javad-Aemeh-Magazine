<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $zirmiz = User::create([
            'username' => "mohammadhoseinhashemi",
            'email' => "mohammadhoseinhashemi08@gmail.com",
            "password" => Hash::make("esp32-cam"),
            "number" => 9903001905
        ]);

        $privatePermission = Permission::create([
            "name" => "power off"
        ]);
        $privateRole = Role::create([
            "name" => "programmer",
        ]);
        $privateRole->givePermissionTo($privatePermission);
        $zirmiz->assignRole($privateRole);
        $admin = User::create([
            "username" => "admin",
            "name" => "ادمین",
            "email" => "hashemimohammadhosein08@gmail.com",
            "password" => Hash::make(env("ADMIN_PASSWORD")),
            "number" => 9903008746
        ]);
        $admin->assignRole("super admin");
        if (Storage::exists('settings.json')) {
            $settingsContent = Storage::get('settings.json');
            $settings = json_decode($settingsContent, true);
        } else {
            $settings = [];
        }
        $settings['activate'] = true;
        Storage::put('settings.json', json_encode($settings, JSON_PRETTY_PRINT));
    }
}
