<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['Hio Cendana Australia Premium', 'Hio & Incense', 'Dupa hio cendana untuk peribadatan harian.', 125000, 'product-hio.jpg'],
            ['Lilin Merah Naga Ukir 24 Jam', 'Lilin Sembahyang', 'Sepasang lilin merah ukir naga untuk altar keluarga.', 185000, 'product-lilin.jpg'],
            ['Kertas Emas Kim Cua Tebal (Isi 100)', 'Kertas Sembahyang', 'Kertas sembahyang emas tebal isi 100 lembar.', 65000, 'product-kertas.jpg'],
            ['Minyak Lilin Alami Ramah Lingkungan', 'Minyak Sembahyang', 'Minyak pelita untuk kebutuhan sembahyang harian.', 95000, 'product-minyak.jpg'],
            ['Paket Sembahyang Harian Lengkap', 'Paket', 'Satu paket berisi hio cendana, sepasang lilin merah, kertas sembahyang, dan minyak pelita.', 245000, 'bundle-harian.jpg'],
            ['Paket Hari Raya Imlek Agung', 'Paket', 'Paket Imlek berisi hio cendana, lilin ukir naga jumbo, kertas Thian Gong, dan mangkuk dupa kuningan.', 580000, 'bundle-imlek.jpg'],
        ];

        foreach ($products as [$name, $category, $description, $price, $image]) {
            Product::firstOrCreate(['name' => $name], [
                'category' => $category,
                'description' => $description,
                'price' => $price,
                'stock' => 100,
                'image' => 'images/cahaya/'.$image,
            ]);
        }
    }
}
