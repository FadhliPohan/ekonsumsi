<?php

namespace Database\Seeders;

use App\Models\masterData\Departemen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;


class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Departemen Umum (wajib ada untuk workflow event)
        Departemen::create([
            'uuid' => Str::uuid(),
            'name' => 'Departemen Umum',
            'code_departement' => 'DEP-UMUM',
            'location' => 'Palembang',
            'is_active' => true,
            'description' => 'Departemen Umum - mengelola konsumsi dan logistik',
        ]);

        for ($i = 0; $i < 20; $i++) {
            Departemen::create([
                'uuid' => Str::uuid(),
                'name' => 'Departemen ' . $faker->jobTitle,
                'code_departement' => 'DEP-' . strtoupper($faker->bothify('####')),
                'location' => $faker->city,
                'is_active' => $faker->boolean(90), // 90% chance of being active
                'description' => $faker->sentence,
            ]);
        }
    }
}
