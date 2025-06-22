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

        if (User::count() == 0) {
            foreach (self::$seeds as $seed) {
                $this->call($seed);
            }
        }
        // if (false) {
            // if (env("APP_ENV") == "local") {
            //     $this->call(localSeeder::class);
            // }
            // if (env("APP_ENV")=='local') {
            //     $this->call(ProdSeeder::class);
            // }
        // }
    }
}
