<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            RolePermissionSeeder::class,
            // SaldoPermissionSeeder::class,
            DepartemenSeeder::class,
            UserSeeder::class,
            FoodSeeder::class,
            SaldoSeeder::class,
            EventSeeder::class,
        ]);
    }
}
