<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            Product::create([
                'name' => 'Product ' . ($i + 1),
                'price' => rand(300, 500) / 100 // Random price (with decimals) between 3 and 5 euros
            ]);
        }
    }
}
