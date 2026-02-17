<?php

namespace Database\Seeders;

use App\Models\masterData\Food;
use App\Models\masterData\FoodLog;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        $foods = [
            ['name' => 'Nasi Goreng', 'description' => 'Nasi goreng spesial dengan telur dan ayam', 'price' => 25000, 'qty_available' => 50],
            ['name' => 'Mie Goreng', 'description' => 'Mie goreng dengan sayuran segar', 'price' => 22000, 'qty_available' => 40],
            ['name' => 'Ayam Bakar', 'description' => 'Ayam bakar bumbu kecap madu', 'price' => 35000, 'qty_available' => 30],
            ['name' => 'Soto Ayam', 'description' => 'Soto ayam kuah bening dengan nasi', 'price' => 20000, 'qty_available' => 45],
            ['name' => 'Gado-Gado', 'description' => 'Gado-gado dengan bumbu kacang', 'price' => 18000, 'qty_available' => 35],
            ['name' => 'Rendang', 'description' => 'Rendang daging sapi khas Padang', 'price' => 40000, 'qty_available' => 25],
            ['name' => 'Bakso', 'description' => 'Bakso sapi dengan kuah kaldu', 'price' => 20000, 'qty_available' => 40],
            ['name' => 'Es Teh Manis', 'description' => 'Es teh manis segar', 'price' => 5000, 'qty_available' => 100],
            ['name' => 'Jus Jeruk', 'description' => 'Jus jeruk segar tanpa pengawet', 'price' => 12000, 'qty_available' => 60],
            ['name' => 'Kopi Hitam', 'description' => 'Kopi hitam tubruk original', 'price' => 8000, 'qty_available' => 80],
            ['name' => 'Nasi Uduk', 'description' => 'Nasi uduk komplit dengan lauk', 'price' => 22000, 'qty_available' => 35],
            ['name' => 'Pecel Lele', 'description' => 'Lele goreng dengan sambal pecel', 'price' => 25000, 'qty_available' => 30],
        ];

        foreach ($foods as $data) {
            $food = Food::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'qty_available' => $data['qty_available'],
                'is_active' => true,
                'updated_by' => 1,
                'updated_name' => 'Admin User',
            ]);

            FoodLog::create([
                'id_food' => $food->id,
                'type' => 'stock_in',
                'qty' => $data['qty_available'],
                'price_before' => 0,
                'price_after' => $data['price'],
                'qty_before' => 0,
                'qty_after' => $data['qty_available'],
                'description' => 'Stok awal: ' . $data['name'] . ' (' . $data['qty_available'] . ' item, Rp ' . number_format($data['price'], 0, ',', '.') . ')',
                'created_by' => 1,
                'created_name' => 'Admin User',
            ]);
        }
    }
}
