<?php

namespace Database\Seeders;

use App\Models\masterData\Departemen;
use App\Models\saldo\Saldo;
use App\Models\saldo\logSaldo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class SaldoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $departemens = Departemen::where('is_active', 1)->get();

        if ($departemens->isEmpty()) {
            $this->command->warn('⚠ Pastikan Departemen sudah di-seed terlebih dahulu.');
            return;
        }

        foreach ($departemens as $dept) {
            // Skip jika sudah punya saldo
            if (Saldo::where('id_departemen', $dept->id)->exists()) continue;

            $initialSaldo = rand(5, 50) * 1000000; // 5jt - 50jt

            $saldo = Saldo::create([
                'id_departemen' => $dept->id,
                'saldo'         => $initialSaldo,
                'updated_by'    => 1,
                'updated_name'  => 'System Seeder',
            ]);

            // Log saldo awal
            logSaldo::create([
                'id_saldo'     => $saldo->id,
                'saldo'        => $initialSaldo,
                'description'  => 'Saldo awal departemen ' . $dept->name,
                'created_by'   => 1,
                'created_name' => 'System Seeder',
                'updated_by'   => 1,
                'updated_name' => 'System Seeder',
                'status'       => 'masuk',
            ]);

            // Tambah 2-4 mutasi random
            $currentSaldo = $initialSaldo;
            $mutasiCount = rand(2, 4);

            for ($i = 0; $i < $mutasiCount; $i++) {
                $isTopup = $faker->boolean(40); // 40% topup, 60% pengeluaran
                $amount  = rand(1, 10) * 500000; // 500rb - 5jt

                if ($isTopup) {
                    $currentSaldo += $amount;
                    $desc   = 'Top-up saldo: ' . $faker->sentence(3);
                    $status = 'masuk';
                } else {
                    $amount = min($amount, $currentSaldo); // jangan minus
                    $currentSaldo -= $amount;
                    $desc   = 'Pengeluaran: ' . $faker->sentence(3);
                    $status = 'keluar';
                }

                logSaldo::create([
                    'id_saldo'     => $saldo->id,
                    'saldo'        => $isTopup ? $amount : -$amount,
                    'description'  => $desc,
                    'created_by'   => 1,
                    'created_name' => 'System Seeder',
                    'updated_by'   => 1,
                    'updated_name' => 'System Seeder',
                    'status'       => $status,
                ]);
            }

            // Update saldo akhir
            $saldo->update(['saldo' => $currentSaldo]);
        }

        $this->command->info('✅ Saldo seeded for ' . $departemens->count() . ' departemens.');
    }
}
