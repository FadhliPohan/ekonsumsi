<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\masterData\Departemen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $departements = Departemen::where('is_active', true)->get();

        foreach ($departements as $departemen) {
            // 1 Manager per departemen
            $manager = User::create([
                'name' => $faker->name,
                'email' => 'manager.' . strtolower(str_replace(' ', '', $departemen->code_departement)) . '@ekonsumsi.test',
                'password' => Hash::make('password'),
            ]);
            $manager->assignRole('Manager');

            UserDetail::create([
                'id_user' => $manager->id,
                'id_departemen' => $departemen->id,
                'position' => 'Manager',
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'gender' => $faker->randomElement(['L', 'P']),
                'description' => 'Manager ' . $departemen->name,
            ]);

            // 10 Karyawan per departemen
            for ($i = 1; $i <= 5; $i++) {
                $karyawan = User::create([
                    'name' => $faker->name,
                    'email' => 'karyawan' . $i . '.' . strtolower(str_replace(' ', '', $departemen->code_departement)) . '@ekonsumsi.test',
                    'password' => Hash::make('password'),
                ]);
                $karyawan->assignRole('Karyawan');

                UserDetail::create([
                    'id_user' => $karyawan->id,
                    'id_departemen' => $departemen->id,
                    'position' => 'Karyawan',
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                    'gender' => $faker->randomElement(['L', 'P']),
                    'description' => 'Karyawan ' . $departemen->name,
                ]);
            }
        }
    }
}
