<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private static $seeds = [
        RolePermissionSeeder::class,
        SectionSeeder::class,
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::exists()) {
            foreach (self::$seeds as $seed) {
                $this->call($seed);
            }
        }

        // if (app()->environment('local')) {
        //     $this->call(LocalSeeder::class);
        // }
    }
}
