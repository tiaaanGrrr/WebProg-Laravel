<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Dupa',
            'category' => 'Dupa & Lilin',
            'description' => 'Dupa wangi',
            'price' => 45000,
            'stock' => 100,
            'image' => 'https://via.placeholder.com/300x300.png?text=Dupa+Cendana',
        ]);

        Product::create([
            'name' => 'Dupa2',
            'category' => 'Dupa & Lilin2',
            'description' => 'Dupa wangi2',
            'price' => 55000,
            'stock' => 100,
            'image' => 'https://via.placeholder.com/300x300.png?text=Dupa+Cendana',
        ]);
    }
}
